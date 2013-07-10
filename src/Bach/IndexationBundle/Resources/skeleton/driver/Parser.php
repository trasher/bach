<?php 

namespace {{ namespace }};

use Bach\IndexationBundle\Entity\ObjectTree;
use Bach\IndexationBundle\Entity\ObjectSheet;
use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\ParserInterface;

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