<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrShema;
use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile;
use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement;

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
	
	/*public function testAddSolrXMLElement() {
		$expected = $this->sxe->getSolrXMLElementID();
		$actual = $this->sxf->addSolrXMLElement($this->sxe);
		$this->assertEquals($expected, $actual->getPath());
	}*/
}
