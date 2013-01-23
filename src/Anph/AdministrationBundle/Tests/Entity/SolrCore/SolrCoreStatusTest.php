<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrCore;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreResponse;
use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreStatus;

class SolrCoreStatusTest extends \PHPUnit_Framework_TestCase
{
    private $sca;
    private $scs;
    
    public function __construct()
    {
        $this->sca = new SolrCoreAdmin();
    }
    
    public function setUp()
    {
        $this->sca->create('TestCoreStatus');
        $scr = $this->sca->getStatus('TestCoreStatus');
        $this->scs = $scr->getCoreStatus('TestCoreStatus');
    }
    
    public function tearDown()
    {
        $this->sca->delete('TestCoreStatus');
        unset($this->scs);
    }
    
    public function testIsDefaultCore()
    {
        $object = $this->scs->isDefaultCore();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertFalse($object, 'IsDefaultCore error!');
        }
    }
    
    public function testGetInstanceDir()
    {
        $object = $this->scs->getInstanceDir();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals($object, '/var/solr/TestCoreStatus/', 'GetInstanceDir error!');
        }
    }
    
    public function testGetDataDir()
    {
        $object = $this->scs->getdataDir();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals($object, '/var/solr/TestCoreStatus/data/', 'GetDataDir error!');
        }
    }
    
    public function testGetConfig()
    {
        $object = $this->scs->getConfig();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals($object, 'solrconfig.xml', 'GetConfig error!');
        }
    }
    
    public function testGetSchema()
    {
        $object = $this->scs->getSchema();
        if ($object === null) {
            $this->assertTrue(false);
        } else {
            $this->assertEquals($object, 'schema.xml', 'GetSchema error!');
        }
    }
    
    public function testGetStartTime()
    {
        $this->assertInstanceOf('DateTime', $this->scs->getStartTime(), 'GetStartTime error!');
    }
    
    public function testGetUptime()
    {
        $this->assertTrue(true);
    }
    
    public function testGetNumDocs()
    {
        $this->assertEquals($this->scs->getNumDocs(), '0', 'GetNumDocs error!');
    }
    
    public function testGetMaxDocs()
    {
        $this->assertEquals($this->scs->getMaxDoc(), '0', 'GetMaxDocs error!');
    }
    
    public function testGetVersion()
    {
        $this->assertEquals($this->scs->getVersion(), '1', 'GetVersion error!');
    }
    
    public function testGetSegmentCount()
    {
        $this->assertEquals($this->scs->getSegmentCount(), '0', 'GetSegmentCount error!');
    }
    
    public function testGetCurrent()
    {
        $this->assertTrue($this->scs->getCurrent(), 'GetCurrent error!');
    }
    
    public function testHasDeletions()
    {
        $this->assertFalse($this->scs->hasDeletions(), 'HasDeletions error!');
    }
    
    public function testGetDirectory()
    {
        $this->assertNotEquals($this->scs->getDirectory(), null, 'GetDirectory error!');
    }
    
    public function testGetSizeInBytes()
    {
        $this->assertEquals($this->scs->getSizeInBytes(), '65', 'GetSizeInBytes error!');
    }
    
    public function testGetSize()
    {
        $this->assertEquals($this->scs->getSize(), '65 bytes', 'GetSize error!');
    }
}
