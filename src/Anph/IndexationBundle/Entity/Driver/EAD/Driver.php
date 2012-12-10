<?php

namespace Anph\IndexationBundle\Entity\Driver\EAD;

use Anph\IndexationBundle\Entity\FileDriver;
use Anph\IndexationBundle\Entity\DataBag;
use Anph\IndexationBundle\Entity\ObjectTree;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;

class Driver extends FileDriver
{	
	/**
	* {@inheritdoc }
	*/
	public function process(DataBag $bag){
		$parserClass = "Anph\IndexationBundle\Entity\Driver\EAD\Parser\\".strtoupper($bag->getType())."\Parser";
		
		if (!class_exists($parserClass)) {
			throw new UnknownDriverParserException(strtoupper($bag->getType()));
		}
		
		$parser = new $parserClass($bag, $this->configuration);
		$tree = $parser->getTree();
		return $this->processTree($tree);
	}
	
	/**
	* {@inheritdoc }
	*/
	public function getFileFormatName(){
		return "ead";
	}
	
	/**
	* Process the object tree returned by the parser
	* @param ObjectTree $tree The parser's tree
	* @return array Data parsed
	*/
	private function processTree(ObjectTree $tree){
		$results = array();
		
		$result = array();
		$result['header'] = $tree->get('header')->getContent()->getValues();
		$result['archdesc'] = $tree->get('archdesc')->getContent()->getValues();
		$results[] = $result;
		
		return $results;
	}
}
