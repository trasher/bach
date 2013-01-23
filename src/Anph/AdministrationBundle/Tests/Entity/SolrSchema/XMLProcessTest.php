<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrSchema;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use DOMDocument;

class XMLProcessTest extends \PHPUnit_Framework_TestCase
{
    private $xmlP;
    private $sca;
    
    public function __construct()
    {
        $this->sca = new SolrCoreAdmin();
        $this->sca->create('testCore');
        $this->xmlP = new XMLProcess('testCore');
    }
    
    public function __destruct()
    {
        $this->sca->unload('testCore');
    }
    
    public function testloadXML()
    {
        $test = $this->xmlP->loadXML();
        $this->assertNotNull($test);
        $attrs = $test->getAttributes();
        $this->assertEquals(count($attrs), 2);
        $this->assertEquals($attrs[0]->getName(), 'name');
        $this->assertEquals($attrs[1]->getName(), 'version');
        $this->assertEquals($attrs[0]->getValue(), 'example core zero');
        $this->assertEquals($attrs[1]->getValue(), '1.1');
        $children = $test->getElements();
        $this->assertEquals(count($children), 5);
        $this->assertEquals($children[0]->getName(), 'types');
        $types = $children[0];
        $this->assertEquals($children[1]->getName(), 'fields');
        $this->assertEquals($children[2]->getName(), 'uniqueKey');
        $uniqueKey = $children[2];
        $this->assertEquals($uniqueKey->getValue(), 'id');
        $this->assertEquals($children[3]->getName(), 'defaultSearchField');
        $defaultSearchField = $children[3];
        $this->assertEquals($defaultSearchField->getValue(), 'name');
        $this->assertEquals($children[4]->getName(), 'solrQueryParser');
        $solrQueryParser = $children[4];
        $attrs = $solrQueryParser->getAttributes();
        $this->assertEquals(count($attrs), 1);
        $this->assertEquals($attrs[0]->getName(), 'defaultOperator');
        
    }
    
    public function testExportXML()
    {
        $doc = new DOMDocument();
        $doc->load('/var/solr/testCore/conf/schema.xml');
        $expected = $doc->saveXML();
        $test = $this->xmlP->saveXML();
        $doc->load('/var/solr/testCore/conf/schema.xml');
        $actual = $doc->saveXML();
        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }
}
