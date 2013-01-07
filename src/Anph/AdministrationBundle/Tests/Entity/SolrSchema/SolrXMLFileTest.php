<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrSchema;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class SolrXMLFileTest extends \PHPUnit_Framework_TestCase
{
	private $sxf;
	private $sxe;
	
	public function setUp()
	{
		$this->sxf = new SolrXMLFile();
		$this->sxe = new SolrXMLElement();
	}
	
	public function tearDown()
	{
		unset($this->sxf);
		unset($this->sxe);
	}
	
	public function testSetCore()
	{
		$expected = 'Bart';
		$actual = $this->sxf->setCore('Bart');
		$this->assertEquals($expected, $actual->getCore());
	}
	
	public function testGetCore()
	{
		$this->sxf->setCore('Bart');
		$expected = 'Bart';
		$actual = $this->sxf->getCore();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetPath()
	{
		$expected = 'Bart';
		$actual = $this->sxf->setPath('Bart');
		$this->assertEquals($expected, $actual->getPath());
	}
	
	public function testGetPath()
	{
		$this->sxf->setPath('Bart');
		$expected = 'Bart';
		$actual = $this->sxf->getPath();
		$this->assertEquals($expected, $actual);
	}
	
	public function testAddSolrXMLElement()
	{
		$elements = $this->sxf->getElements();
		$this->assertTrue(count($elements) == 0);
		$this->sxf->addElement($this->sxe);
		$elements = $this->sxf->getElements();
		$this->assertTrue(count($elements) == 1);
	}
	
	public function testRemoveSolrXMLElementWithNoElements()
	{
	    $this->sxf->addElement($this->sxe);
	    $this->sxf->removeElement($this->sxe);
	    $elements = $this->sxf->getElements();
	    $this->assertTrue(count($elements) == 0);
	}
	
	public function testRemoveSolrXMLElementWithOneElement()
	{
	    $this->sxf->addElement($this->sxe);
	    $this->sxf->addElement(new SolrXMLElement());
	    $this->sxf->removeElement($this->sxe);
	    $elements = $this->sxf->getElements();
	    $this->assertTrue(count($elements) == 1);
	}
	
	public function testGetElement()
	{
	    $expected = $this->sxe->getSolrXMLElementID();
	    $this->sxf->addElement($this->sxe);
	    $elements = $this->sxf->getElements();
	    $elmt = $elements[0];
	    $actual = $elmt->getSolrXMLElementID();
	    $this->assertEquals($expected, $actual);
	}
}
