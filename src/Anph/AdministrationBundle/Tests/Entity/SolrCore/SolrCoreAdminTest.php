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
        $response = $this->sca->create('core2');
        if ($response === true) {
            $this->assertTrue(true);
        } else {
            echo "###BAAM###";
            echo 'Error message : ' . $response->getMessage();
            echo 'Trace : ' . $response->getTrace() !== null ? $response->getTrace() : 'NULL';
            echo 'Code : ' .$response->getCode();
            echo "###BOOM###";
            $this->assertTrue(false);
        }   
    }
   /* 
    public function testGetStatusAllCore() {
        $response = $this->sca->getStatus();
        echo 'STATUS ALL : ###' . $response . '###';
        $this->assertTrue(true);
    }
    
    public function testGetStatusOneCore() {
        $response = $this->sca->getStatus('core0');
        echo 'STATUS ONE : ###' . $response . '###';
        $this->assertTrue(true);
    }
    
    public function testReload() {
        $response = $this->sca->reload('core0');
        echo 'RELOAD : ###' . $response . '###';
        $this->assertTrue(true);
    }
    
    public function testRename() {
        $response = $this->sca->create('coreTest');
        $response = $this->sca->rename('coreTest', 'coreNewTest');
        echo 'RENAME : ###' . $response . '###';
        $this->assertTrue(true);
    }*/
}