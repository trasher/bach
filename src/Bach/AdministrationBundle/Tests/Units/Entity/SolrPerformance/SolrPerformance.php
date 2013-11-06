<?php
/**
 * Bach SolrPerformance unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrPerformance;

use atoum\AtoumBundle\Test\Units;
use Symfony\Component\Yaml\Parser;
use Bach\AdministrationBundle\Entity\SolrCore\BachCoreAdminConfigReader;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance as Perf;
use DOMDocument;

/**
 * Bach SolrPerformance unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrPerformance extends Units\Test
{
    private $_sp;
    private $_sca;
    private $_doc;
    private $_xml_path;

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
        $config_reader = new BachCoreAdminConfigReader(
            false,
            $this->params['solr_host'],
            $this->params['solr_port'],
            $this->params['solr_path'],
            'app/cache/test',
            'app'
        );

        $this->_sca = new SolrCoreAdmin($config_reader);
        $this->_xml_path = $config_reader
            ->getConfDir($this->params['solr_search_core']) . 'solrconfig.xml';

        $this->_sp = new Perf($this->_sca, $this->params['solr_search_core']);
        $this->_doc = new DOMDocument();
        $this->_doc->load($this->_xml_path);
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct()
    {
        $sp = new Perf($this->_sca, $this->params['solr_search_core']);
        $this->variable($sp)->isNotNull();
    }

    /*public function testSetQueryResultWindowsSizeWhenNotExist()
    {
        $expected = 1000;
        $response = $this->_sp->setQueryResultWindowsSize($expected);

        $this->variable($response)->isNotNull();
        //$this->saveAndLoad($response);
        $nodeList = $this->_doc->getElementsByTagName(Perf::QUERY_RESULT_WIN_SIZE_TAG);
        $actual = $nodeList->item(0)->nodeValue;

        $this->string($actual)->isEqualTo($expected);
        //if ($nodeList->length == 0) {
        //    $this->assertTrue(false);
        //} else {
        //    $actual = $nodeList->item(0)->nodeValue;
        //    $this->assertEquals($expected, $actual);
        //}
    }*/

    /*public function testSetQueryResultWindowsSizeWhenExist()
    {
        $expected = 1000;
        $this->sp->setQueryResultWindowsSize(2000);
        $this->saveAndLoad(true);
        $response = $this->sp->setQueryResultWindowsSize($expected);
        $this->assertNotNull($response);
        $this->saveAndLoad($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::QUERY_RESULT_WIN_SIZE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $actual = $nodeList->item(0)->nodeValue;
            $this->assertEquals($expected, $actual);
        }
    }

    public function testGetQueryResultWindowsSize()
    {
        $this->sp->setQueryResultWindowsSize(1001);
        $actual = $this->sp->getQueryResultWindowsSize();
        $this->assertNotNull($actual);
        if ($actual != null) {
            $this->assertEquals('1001', $actual);
        }
    }

    public function testSetQueryResultMaxDocsCachedWhenNotExist()
    {
        $expected = 1000;
        $response = $this->sp->setQueryResultMaxDocsCached($expected);
        $this->assertNotNull($response);
        $this->saveAndLoad($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::QUERY_RESULT_MAX_DOCS_CACHED_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $actual = $nodeList->item(0)->nodeValue;
            $this->assertEquals($expected, $actual);
        }
    }

    public function testSetQueryResultMaxDocsCachedWhenExist()
    {
        $expected = 1000;
        $this->sp->setQueryResultMaxDocsCached(2000);
        $this->saveAndLoad(true);
        $response = $this->sp->setQueryResultMaxDocsCached($expected);
        $this->assertNotNull($response);
        $this->saveAndLoad($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::QUERY_RESULT_MAX_DOCS_CACHED_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $actual = $nodeList->item(0)->nodeValue;
            $this->assertEquals($expected, $actual);
        }
    }

    public function testGetQueryResultMaxDocsCached()
    {
        $this->sp->setQueryResultMaxDocsCached(1002);
        $actual = $this->sp->getQueryResultMaxDocsCached();
        $this->assertNotNull($actual);
        if ($actual != null) {
            $this->assertEquals('1002', $actual);
        }
    }

    public function testSetDocumentCacheParametersWhenNotExist()
    {
        $response = $this->sp->setDocumentCacheParameters('MyClass', 100, 80);
        $this->saveAndLoad($response);
        $this->assertNotNull($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::DOCUMENT_CACHE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals('MyClass', $nodeList->item(0)->getAttribute('class'));
            $this->assertEquals('100', $nodeList->item(0)->getAttribute('size'));
            $this->assertEquals('80', $nodeList->item(0)->getAttribute('initialSize'));
        }
    }

    public function testSetDocumentCacheParametersWhenExist()
    {
        $this->sp->setDocumentCacheParameters('MyClass2', 1000, 800);
        $this->saveAndLoad(true);
        $response = $this->sp->setDocumentCacheParameters('MyClass', 100, 80);
        $this->saveAndLoad($response);
        $this->assertNotNull($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::DOCUMENT_CACHE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals('MyClass', $nodeList->item(0)->getAttribute('class'));
            $this->assertEquals('100', $nodeList->item(0)->getAttribute('size'));
            $this->assertEquals('80', $nodeList->item(0)->getAttribute('initialSize'));
        }
    }

    public function testGetDocumentCacheParameters()
    {
        $this->sp->setDocumentCacheParameters('bambam', 1, 2);
        $actual = $this->sp->getDocumentCacheParameters();
        $this->assertNotNull($actual);
        if ($actual != null) {
            $this->assertEquals('bambam', $actual[0]);
            $this->assertEquals('1', $actual[1]);
            $this->assertEquals('2', $actual[2]);
        }
    }

    public function testSetQueryResultCacheParametersWhenNotExist()
    {
        $response = $this->sp->setQueryResultCacheParameters('MyClass', 100, 80, 70);
        $this->saveAndLoad($response);
        $this->assertNotNull($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::QUERY_RESULT_CACHE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals('MyClass', $nodeList->item(0)->getAttribute('class'));
            $this->assertEquals('100', $nodeList->item(0)->getAttribute('size'));
            $this->assertEquals('80', $nodeList->item(0)->getAttribute('initialSize'));
            $this->assertEquals('70', $nodeList->item(0)->getAttribute('autowarmCount'));
        }
    }

    public function testSetQueryResultCacheParametersWhenExist()
    {
        $this->sp->setQueryResultCacheParameters('MyClass2', 1000, 800, 700);
        $this->saveAndLoad(true);
        $response = $this->sp->setQueryResultCacheParameters('MyClass', 100, 80, 70);
        $this->saveAndLoad($response);
        $this->assertNotNull($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::QUERY_RESULT_CACHE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals('MyClass', $nodeList->item(0)->getAttribute('class'));
            $this->assertEquals('100', $nodeList->item(0)->getAttribute('size'));
            $this->assertEquals('80', $nodeList->item(0)->getAttribute('initialSize'));
            $this->assertEquals('70', $nodeList->item(0)->getAttribute('autowarmCount'));
        }
    }

    public function testGetQueryResultCacheParameters()
    {
        $this->sp->setQueryResultCacheParameters('bambam', 1, 2, 3);
        $actual = $this->sp->getQueryResultCacheParameters();
        $this->assertNotNull($actual);
        if ($actual != null) {
            $this->assertEquals('bambam', $actual[0]);
            $this->assertEquals('1', $actual[1]);
            $this->assertEquals('2', $actual[2]);
            $this->assertEquals('3', $actual[3]);
        }
    }

    public function testSetFilterCacheParametersNotExist()
    {
        $response = $this->sp->setFilterCacheParameters('MyClass', 100, 80, 70);
        $this->saveAndLoad($response);
        $this->assertNotNull($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::FILTER_CACHE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals('MyClass', $nodeList->item(0)->getAttribute('class'));
            $this->assertEquals('100', $nodeList->item(0)->getAttribute('size'));
            $this->assertEquals('80', $nodeList->item(0)->getAttribute('initialSize'));
            $this->assertEquals('70', $nodeList->item(0)->getAttribute('autowarmCount'));
        }
    }

    public function testSetFilterCacheParametersWhenExist()
    {
        $this->sp->setFilterCacheParameters('MyClass2', 1000, 800, 700);
        $this->saveAndLoad(true);
        $response = $this->sp->setFilterCacheParameters('MyClass', 100, 80, 70);
        $this->saveAndLoad($response);
        $this->assertNotNull($response);
        $nodeList = $this->doc->getElementsByTagName(SolrPerformance::FILTER_CACHE_TAG);
        if ($nodeList->length == 0) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals('MyClass', $nodeList->item(0)->getAttribute('class'));
            $this->assertEquals('100', $nodeList->item(0)->getAttribute('size'));
            $this->assertEquals('80', $nodeList->item(0)->getAttribute('initialSize'));
            $this->assertEquals('70', $nodeList->item(0)->getAttribute('autowarmCount'));
        }
    }

    public function testGetFilterCacheParameters()
    {
        $this->sp->setFilterCacheParameters('bambam', 1, 2, 3);
        $actual = $this->sp->getFilterCacheParameters();
        $this->assertNotNull($actual);
        if ($actual != null) {
            $this->assertEquals('bambam', $actual[0]);
            $this->assertEquals('1', $actual[1]);
            $this->assertEquals('2', $actual[2]);
            $this->assertEquals('3', $actual[3]);
        }
    }*/

    /**
     * Save and load response
     *
     * @param mixed $response Response
     *
     * @return void
     */
    /*private function _saveAndLoad($response)
    {
        if ($response !== null) {
            $this->_sp->save();
            $this->_doc->load($this->_xml_path);
        }
    }*/
}
