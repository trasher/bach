<?php
/**
 * Bach XMLProcess unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrSchema;

use atoum\AtoumBundle\Test\Units;
use Symfony\Component\Yaml\Parser;
use Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess as Process;
use DOMDocument;

/**
 * Bach XMLProcess unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class XMLProcess extends Units\Test
{
    private $_xmlp;
    private $_sca;
    protected $conf;
    protected $params;

    /**
     * Set up tests
     *
     * @param stgring $testMethod Method tested
     *
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $yaml = new Parser();

        //load main configuration
        $config_file = 'app/config/defaults_parameters.yml';
        $this->conf = $yaml->parse(
            file_get_contents($config_file)
        );

        //load local configuration
        $config_file = 'app/config/parameters.yml';
        $this->conf = array_replace_recursive(
            $this->conf,
            $yaml->parse(
                file_get_contents($config_file)
            )
        );

        $this->params = $this->conf['parameters'];
        if ( $this->params['solr_search_core'] === '%ead_corename%' ) {
            $this->params['solr_search_core'] = $this->params['ead_corename'];
        }

        $config_reader = new BachCoreAdminConfigReader(
            false,
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );

        $this->_sca = new SolrCoreAdmin($config_reader);
        $this->_xmlp = new Process($this->_sca, $this->params['solr_search_core']);
    }

    /**
     * Test construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $xmlp = new Process($this->_sca, $this->params['solr_search_core']);
        $root = $xmlp->getRootElement();
        $file_path = $xmlp->getFilePath();

        $this->variable($root)->isNotNull();
        $this->string($file_path)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/conf/schema.xml#'
        );
    }

    /**
     * Test load XML
     *
     * @return void
     */
    public function testloadXML()
    {
        $test = $this->_xmlp->loadXML();

        //var_dump($test);

        $this->variable($test)->isNotNull();

        $attrs = $test->getAttributes();
        $this->array($attrs)->hasSize(2);

        $name = $attrs[0];
        $version = $attrs[1];

        $this->string($name->getName())->isIdenticalTo('name');
        $this->string($version->getName())->isIdenticalTo('version');

        $this->string($name->getValue())->isNotEmpty();
        $this->string($version->getValue())->isNotEmpty();

        $children = $test->getElements();

        $this->array($children)->size->isGreaterThan(30);

        $types = $children[0];
        $this->string($types->getName())->isIdenticalTo('types');

        $unique = $children[1];
        $this->string($unique->getName())->isIdenticalTo('uniqueKey');

        $fields = $children[2];
        $this->string($fields->getName())->isIdenticalTo('fields');

        $copy = $children[3];
        $this->string($copy->getName())->isIdenticalTo('copyField');

        $copy_last = $children[count($children) -1];
        $this->string($copy_last->getName())->isIdenticalTo('copyField');
    }

    /**
     * Test XML output
     *
     * @return void
     */
    public function testExportXML()
    {
        $doc = new DOMDocument();

        $xml_path = $this->_sca->getSchemaPath($this->params['solr_search_core']);
        $doc->load($xml_path);

        $actual = $this->_xmlp->saveXML();

        $this->object($actual)->isEqualTo($doc);
    }

    /**
     * Test get Elements by name
     *
     * @return void
     */
    public function testGetElementsByname()
    {
        $actual = $this->_xmlp->getElementsByName('fieldType');

        $this->array($actual)
            ->hasSize(23);

        $node = $actual[0];
        $this->string($node->getName())->isIdenticalTo('fieldType');
    }
}
