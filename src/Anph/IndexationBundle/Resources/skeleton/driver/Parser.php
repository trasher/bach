<?php 

namespace {{ namespace }}

use Anph\IndexationBundle\Entity\ObjectTree;
use Anph\IndexationBundle\Entity\ObjectSheet;
use Anph\IndexationBundle\Entity\DataBag;
use Anph\IndexationBundle\ParserInterface;

class Parser implements ParserInterface{
	
	private $tree;
	private $configuration;
	
	/**
	* {@inheritdoc }
	*/
	public function __construct(DataBag $bag, $configuration){
		$this->configuration = $configuration;
		$this->tree = new ObjectTree("root");
		$this->parse($bag);
	}
	
	/**
	* {@inheritdoc }
	*/
	public function parse(DataBag $bag){
		
	}
	
	/**
	* {@inheritdoc }
	*/
	public function getTree(){
		return $this->tree;
	}
}
?>