<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrSchema;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class SolrXMLAttributeTest extends \PHPUnit_Framework_TestCase
{
    private $sxe;
    private $sxa;
    
    public function setUp()
    {
        $this->sxe = new SolrXMLElement();
        $this->sxa = new SolrXMLAttribute();
    }
    
    public function tearDown()
    {
        unset($this->sxe);
        unset($this->sxa);
    }
    
    public function testSetName()
	{
		$expected = 'Bart';
		$actual = $this->sxa->setName('Bart');
		$this->assertEquals($expected, $actual->getName());
	}
	
	public function testGetName()
	{
		$this->sxa->setName('Bart');
		$expected = 'Bart';
		$actual = $this->sxa->getName();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetValue()
	{
	    $expected = 'Bart';
	    $actual = $this->sxa->setValue('Bart');
	    $this->assertEquals($expected, $actual->getValue());
	}
	
	public function testGetValue()
	{
	    $this->sxa->setValue('Bart');
	    $expected = 'Bart';
	    $actual = $this->sxa->getValue();
	    $this->assertEquals($expected, $actual);
	}
	
	public function testSetElement()
	{
	    $expected = new SolrXMLElement();
	    $newElement = new SolrXMLElement();
	    $actual = $this->sxa->setElement($newElement);
	    $this->assertEquals($expected, $actual->getElement());
	}
	
	public function testGetElement()
	{
	    $expected = new SolrXMLElement();
	    $this->sxa->setElement($expected);
	    $actual = $this->sxa->getElement();
	    $this->assertEquals($expected, $actual);
	}
}
