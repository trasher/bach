<?php
namespace Anph\AdministrationBundle\Tests\Entity\SolrShema;
use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile;
use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement;
use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute;

class SolrXMLElementTest extends \PHPUnit_Framework_TestCase {
	private $sxf;
	private $sxe;
	private $sxa;
	
	public function setUp() {
		$this->sxf = new SolrXMLFile();
		$this->sxe = new SolrXMLElement();
		$this->sxa = new SolrXMLAttribute();
	}
	
	public function tearDown() {
		unset($this->sxf);
		unset($this->sxe);
		unset($this->sxa);
	}
	
	public function testSetBalise() {
		$expected = 'Bart';
		$actual = $this->sxe->setBalise('Bart');
		$this->assertEquals($expected, $actual->getBalise());
	}
	
	public function testGetBalise() {
		$this->sxe->setBalise('Bart');
		$expected = 'Bart';
		$actual = $this->sxe->getBalise();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetValue() {
		$expected = 'Bart';
		$actual = $this->sxe->setValue('Bart');
		$this->assertEquals($expected, $actual->getValue());
	}
	
	public function testGetValue() {
		$this->sxe->setValue('Bart');
		$expected = 'Bart';
		$actual = $this->sxe->getValue();
		$this->assertEquals($expected, $actual);
	}
	
	public function testSetFile() {
		$expected = $this->sxf;
		$actual = $this->sxe->setFile($this->sxf);
		$this->assertEquals($expected, $actual->getFile());
	}
	
	public function testGetFile() {
		$this->sxe->setFile($this->sxf);
		$expected = $this->sxf;;
		$actual = $this->sxe->getFile();
		$this->assertEquals($expected, $actual);
	}
}
