<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrPerformance;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance;
use DOMDocument;

class SolrPerformanceTest extends \PHPUnit_Framework_TestCase
{
    private $sp;
    private $sca;
    private $doc;
    
    public function __construct()
    {
        $this->sca = new SolrCoreAdmin();
        $this->sca->create('coreTest');
        $this->sp = new SolrPerformance('/var/solr/coreTest/conf/solrconfig.xml');
        $this->doc = new DOMDocument();
        $this->doc->load('/var/solr/coreTest/conf/solrconfig.xml');
        
    }
    
    public function __destruct()
    {
        $this->sca->delete('coreTest');
    }
    
    public function testSetQueryResultWindowsSizeWhenNotExist()
    {
        $expected = 1000;
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
    
    public function testSetQueryResultWindowsSizeWhenExist()
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
    
    public function testQueryResultMaxDocsCachedWhenNotExist()
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
    
    public function testQueryResultMaxDocsCachedWhenExist()
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
    
    private function saveAndLoad($response)
    {
        if ($response !== null) {
            $this->sp->save();
            $this->doc->load('/var/solr/coreTest/conf/solrconfig.xml');
        }
    }
}
