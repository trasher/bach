<?php
/**
 * Bach SolrCoreStatus unit tests
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
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreStatus as CoreStatus;

/**
 * Bach SolrCoreStatus unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrCoreStatus extends Units\Test
{
    private $_sca;
    private $_scs;

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
        $this->_scs = $this->_sca->getStatus($this->params['solr_search_core'])->getCoreStatus($this->params['solr_search_core']);
    }

    /**
     * Test if core is default
     *
     * @return void
     */
    /*public function testIsDefaultCore()
    {
        $is_default = $this->_scs->isDefaultCore();
        $this->boolean($is_default)->isFalse();
    }*/

    /*public function testGetInstanceDir()
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
    }*/
}
