<?php
namespace Bach\AdministrationBundle\Tests\Entity;

use Bach\AdministrationBundle\Entity\BachCoreAdminConfigReader;

class BachCoreAdminConfigReaderTest extends \PHPUnit_Framework_TestCase
{
    private $bcacr;
    
    public function __construct() {
        $this->bcacr = new BachCoreAdminConfigReader();
    }
    
    public function testGetCoresPath()
    {
        $expected = $this->bcacr->getCoresPath();
        $actual = '/var/solr/';
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetCoresURL()
    {
        $expected = $this->bcacr->getCoresURL();
        $actual = 'http://localhost:8080/solr/admin/cores';
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetCoreTemplatePath()
    {
        $expected = $this->bcacr->getCoreTemplatePath();
        $actual = '/var/solr/coreTemplate';
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetCoreDataDir()
    {
        $expected = $this->bcacr->getCoreDataDir();
        $actual = 'data';
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetCoreConfigDir()
    {
        $expected = $this->bcacr->getCoreConfigDir();
        $actual = 'conf';
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetConfigFileName()
    {
        $expected = $this->bcacr->getConfigFileName();
        $actual = 'solrconfig.xml';
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetSchemaFileName()
    {
        $expected = $this->bcacr->getSchemaFileName();
        $actual = 'schema.xml';
        $this->assertEquals($expected, $actual);
    }
}
