<?php 

namespace Anph\IndexationBundle\Entity\Driver\EAD\Parser\XML;

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
		$dom = $bag->getData();
		$xpath = new \DOMXPath($dom);
		
		$headerNode = new EADHeader($xpath,
									$xpath->query('/ead/eadheader')->item(0),
									$this->configuration['fields']['header']);
		$this->tree->append(new ObjectSheet("header",$headerNode));
		
		$archDescNode = new EADArchDesc($xpath,
										$xpath->query('/ead/archdesc')->item(0), 
										$this->configuration['fields']['archdesc']);
		$this->tree->append(new ObjectSheet("archdesc",$archDescNode));	
	}
	
	/**
	* {@inheritdoc }
	*/
	public function getTree(){
		return $this->tree;
	}
}
?>