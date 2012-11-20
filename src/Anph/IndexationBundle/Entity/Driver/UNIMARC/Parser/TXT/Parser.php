<?php 

namespace Anph\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT

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
		$content = $bag->getData();
		$labelPart = substr($content,0,24);
		$label = new Label($labelPart);
		$tree->append('label', $label);
		
		$indice = strpos($content, '');
		$size  = strlen($content);
		//echo ("jfolflflflfllflflfl" .$size);
		$blocIdent = substr($content, 24, $indice-24);
		$blocInfo = substr($content, $indice, $size - $indice);
		$ident = new Identification($blocIdent, $blocInfo);
		
		$tree->append('identification',$ident);
	}
	
	/**
	* {@inheritdoc }
	*/
	public function getTree(){
		return $this->tree;
	}
}
?>