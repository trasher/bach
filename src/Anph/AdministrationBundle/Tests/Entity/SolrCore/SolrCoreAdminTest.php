<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrCore;
use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

class SolrCoreAdminTest extends \PHPUnit_Framework_TestCase
{
    private $sca;
    
    public function setUp() {
        $this->sca = new SolrCoreAdmin();
    }
    
    public function tearDown() {
        unset($this->sca);
    }
    
    public function testCreate() {
        $response = $this->sca->create('coreTest');
        $this->assertTrue($response->isOk()); 
    }
    
    public function testGetStatusAllCore() {
        $response = $this->sca->getStatus();
        $this->assertTrue($response->isOk());
    }
    
    public function testGetStatusOneCore() {
        $response = $this->sca->getStatus('core0');
        $this->assertTrue($response->isOk());
    }
    
    public function testReload() {
        $response = $this->sca->reload('core0');
        if ($response->isOk()) {
            $this->assertTrue(true);
        } else {
            echo '#####RELOAD#####';
            echo 'MSG :' . $response->getMessage() . '#####';
            echo 'CODE :' . $response->getCode() . '#####';
            echo 'TRACE :' . $response->getTrace() . '#####';
            $this->assertTrue(false);
        }
    }
/*    
    public function testRename() {
        $response = $this->sca->create('coreTest');
        $response = $this->sca->rename('coreTest', 'coreNewTest');
        echo 'RENAME : ###' . $response . '###';
        $this->assertTrue(true);
    }*/
}