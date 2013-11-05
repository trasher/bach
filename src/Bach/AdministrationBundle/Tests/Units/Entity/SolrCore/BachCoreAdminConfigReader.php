<?php
/**
 * Bach BachCoreAdminConfigReader unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrCore;

use atoum\AtoumBundle\Test\Units;
use Symfony\Component\Yaml\Parser;
use Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader as ConfigReader;

/**
 * Bach BachCoreAdminConfigReader unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachCoreAdminConfigReader extends Units\Test
//class BachCoreAdminConfigReaderTest extends \PHPUnit_Framework_TestCase
{
    private $_bcacr;
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
        //load configuration
        $config_file = 'app/config/parameters.yml';
        $yaml = new Parser();
        $this->conf = $yaml->parse(
            file_get_contents($config_file)
        );

        $this->params = $this->conf['parameters'];
        $this->_bcacr = new ConfigReader(
            false, 
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );
    }

    /**
     * Test constructor and stream loading
     *
     * @return void
     */
    public function testConstruct()
    {
        $bcacr = new ConfigReader(
            false, 
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );

        $this->object($bcacr)->isInstanceof(
            'Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader'
        );
    }

    /**
     * Test serialization
     *
     * @return void
     */
    public function testSerialization()
    {
        $serial = serialize($this->_bcacr);
        $bool = is_string($serial);

        $this->boolean($bool)->isTrue();

        $unserial = unserialize($serial);
        $this->object($unserial)->isInstanceof(
            'Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader'
        );
    }

    /**
     * Test cores path
     *
     * @return void
     */
    public function testGetCoresPath()
    {
        $actual = $this->_bcacr->getCoresPath();
        $this->string($actual)->isNotEmpty();
    }

    /**
     * Test cores URL
     *
     * @return void
     */
    public function testGetCoresURL()
    {
        $actual = $this->_bcacr->getCoresURL();

        $url_pattern = 'http%ssl://%host:%port%path';
        $url = str_replace(
            array(
                '%ssl',
                '%host',
                '%port',
                '%path'
            ),
            array(
                ($this->params['solr_ssl'] == true) ? 's' : '',
                $this->params['solr_host'],
                $this->params['solr_port'],
                $this->params['solr_path']
            ),
            $url_pattern
        );

        $this->string($actual)->isIdenticalTo($url);
    }

    /**
     * Test core templates path
     *
     * @return void
     */
    public function testGetCoreTemplatePath()
    {
        $actual = $this->_bcacr->getCoreTemplatePath();
        $expected = 'app/config/coreTemplate';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test core data dir
     *
     * @return void
     */
    public function testGetCoreDataDir()
    {
        $actual = $this->_bcacr->getDataDir($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/data/$#'
        );
    }

    /**
     * Test core config dir
     *
     * @return void
     */
    public function testGetDefaultConfigDir()
    {
        $actual = $this->_bcacr->getDefaultConfigDir();
        $expected = 'conf/';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test default config file name
     *
     * @return void
     */
    public function testGetDefaultConfigFileName()
    {
        $actual = $this->_bcacr->getDefaultConfigFileName();
        $expected = 'solrconfig.xml';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test schema default file name
     *
     * @return void
     */
    public function testGetDefaultSchemaFileName()
    {
        $actual = $this->_bcacr->getDefaultSchemaFileName();
        $expected = 'schema.xml';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test temporary cores path
     *
     * @return void
     */
    public function testTempCorepath()
    {
        $actual = $this->_bcacr->getTempCorePath();
        $expected = 'app/cache/test/tmpCores/';

        $this->string($actual)->isIdenticalTo($expected);
    }

    /**
     * Test instance dir
     *
     * @return void
     */
    public function testGetInstanceDir()
    {
        $actual = $this->_bcacr->getInstanceDir($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/#'
        );
    }

    /**
     * Test conf dir
     *
     * @return void
     */
    public function testConfDir()
    {
        $actual = $this->_bcacr->getConfDir($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/conf/#'
        );
    }

    /**
     * Test schema path
     *
     * @return void
     */
    public function testSchemaPath()
    {
        $actual = $this->_bcacr->getSchemaPath($this->params['solr_search_core']);

        $this->string($actual)->match(
            '#.*/'  . $this->params['solr_search_core'] . '/conf/schema.xml#'
        );
    }

    /**
     * Test default config filename
     *
     * @return void
     */
    public function testDefaultDataConfigFileName()
    {
        $actual = $this->_bcacr->getDefaultDataConfigFileName();
        $expected = 'data-config.xml';

        $this->string($actual)->isIdenticalTo($expected);
    }
}
