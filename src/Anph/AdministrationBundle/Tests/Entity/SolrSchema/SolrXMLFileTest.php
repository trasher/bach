<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrSchema;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class SolrXMLFileTest extends \PHPUnit_Framework_TestCase {
	private $sxf;
	private $sxe;
	
	public function setUp() {
		$this->sxf = new SolrXMLFile();
		$this->sxe = new SolrXMLElement();
	}
	
	public function tearDown() {
		unset($this->sxf);
		unset($this->sxe);
	}
	
	public function testSetName() {
		$expected = 'Bart';
		$actual = $this->sxf->setName('Bart');
		$this->assertEquals($expected, $actual->getName());
	}
	
	public function testGetName() {
		$this->sxf->setName('Bart');
		$expected = 'Bart';
		$actual = $this->sxf->getName();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetPath() {
		$expected = 'Bart';
		$actual = $this->sxf->setPath('Bart');
		$this->assertEquals($expected, $actual->getPath());
	}
	
	public function testGetPath() {
		$this->sxf->setPath('Bart');
		$expected = 'Bart';
		$actual = $this->sxf->getPath();
		$this->assertEquals($expected, $actual);
	}
	
	public function testAddSolrXMLElement() {
		$expected = $this->sxe->getSolrXMLElementID();
		$this->sxf->addElement($this->sxe);
		$elements = $this->sxf->getElements();
		$this->assertTrue(count($elements) == 1);
		$this->assertInstanceOf('SolrXMLElement', $elements[0]);
		/*$actual = $elements[0]->getSolrXMLElementID();
		$this->assertEquals($expected, $actual);*/
	}
}
