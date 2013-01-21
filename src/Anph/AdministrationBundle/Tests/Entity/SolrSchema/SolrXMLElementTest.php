<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrSchema;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

class SolrXMLElementTest extends \PHPUnit_Framework_TestCase
{
	private $sxf;
	private $sxe;
	private $sxa;
	
	public function setUp()
	{
		$this->sxf = new SolrXMLFile();
		$this->sxe = new SolrXMLElement();
		$this->sxa = new SolrXMLAttribute();
	}
	
	public function tearDown()
	{
		unset($this->sxf);
		unset($this->sxe);
		unset($this->sxa);
	}
	
	public function testSetTag()
	{
		$expected = 'Bart';
		$actual = $this->sxe->setTag('Bart');
		$this->assertEquals($expected, $actual->getTag());
	}
	
	public function testGetTag()
	{
		$this->sxe->setTag('Bart');
		$expected = 'Bart';
		$actual = $this->sxe->getTag();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetValue()
	{
		$expected = 'Bart';
		$actual = $this->sxe->setValue('Bart');
		$this->assertEquals($expected, $actual->getValue());
	}
	
	public function testGetValue()
	{
		$this->sxe->setValue('Bart');
		$expected = 'Bart';
		$actual = $this->sxe->getValue();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetFile()
	{
		$expected = $this->sxf;
		$actual = $this->sxe->setFile($this->sxf);
		$this->assertEquals($expected, $actual->getFile());
	}
	
	public function testGetFile()
	{
		$this->sxe->setFile($this->sxf);
		$expected = $this->sxf;;
		$actual = $this->sxe->getFile();
		$this->assertEquals($expected, $actual);
	}
	
	public function testAddElement()
	{
	    $elements = $this->sxe->getElements();
	    $this->assertTrue(count($elements) == 0);
	    $this->sxe->addElement(new SolrXMLElement());
	    $elements = $this->sxe->getElements();
	    $this->assertTrue(count($elements) == 1);
	}
	
	public function testRemoveElementWithNoElements()
	{
	    $newElement = new SolrXMLElement();
	    $this->sxe->addElement($newElement);
	    $this->sxe->removeElement($newElement);
	    $elements = $this->sxe->getElements();
	    $this->assertTrue(count($elements) == 0);
	}
	
	public function testRemoveElementWithOneElements()
	{
	    $newElement1 = new SolrXMLElement();
	    $newElement2 = new SolrXMLElement();
	    $this->sxe->addElement($newElement1);
	    $this->sxe->addElement($newElement2);
	    $this->sxe->removeElement($newElement1);
	    $elements = $this->sxe->getElements();
	    $this->assertTrue(count($elements) == 1);
	}
	
	public function testAddAttribute()
	{
	    $attributes = $this->sxe->getAttributes();
	    $this->assertTrue(count($attributes) == 0);
	    $this->sxe->addAttribute(new SolrXMLAttribute());
	    $attributes = $this->sxe->getAttributes();
	    $this->assertTrue(count($attributes) == 1);
	}
	
	public function testRemoveAttributeWithNoElements()
	{
	    $newAttribute = new SolrXMLAttribute();
	    $this->sxe->addAttribute($newAttribute);
	    $this->sxe->removeAttribute($newAttribute);
	    $attributes = $this->sxe->getAttributes();
	    $this->assertTrue(count($attributes) == 0);
	}
	
	public function testRemoveAttributeWithOneElements()
	{
	    $newAttribute1 = new SolrXMLAttribute();
	    $newAttribute2 = new SolrXMLAttribute();
	    $this->sxe->addAttribute($newAttribute1);
	    $this->sxe->addAttribute($newAttribute2);
	    $this->sxe->removeAttribute($newAttribute1);
	    $attributes = $this->sxe->getAttributes();
	    $this->assertTrue(count($attributes) == 1);
	}
	
	public function testSetRoot()
	{
	    $expected = new SolrXMLElement();
	    $newRoot = new SolrXMLElement();
	    $actual = $this->sxe->setRoot($newRoot);
	    $this->assertEquals($expected, $actual->getRoot());
	}
	
	public function testGetRoot()
	{
	    $expected = new SolrXMLElement();
	    $this->sxe->setRoot($expected);
	    $actual = $this->sxe->getRoot();
	    $this->assertEquals($expected, $actual);
	}
}
