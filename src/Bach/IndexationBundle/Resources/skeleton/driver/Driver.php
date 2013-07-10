<?php

namespace {{ namespace }};

use Bach\IndexationBundle\Entity\FileDriver;
use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Entity\ObjectTree;
use Bach\IndexationBundle\Exception\UnknownDriverParserException;

class Driver extends FileDriver
{	
	/**
	* {@inheritdoc }
	*/
	public function process(DataBag $bag){
		$parserClass = "Bach\IndexationBundle\Entity\Driver\{{ format_uppercase }}\Parser\\".strtoupper($bag->getType())."\Parser";
		
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
		return "{{ format }}";
	}
	
	/**
	* Process the object tree returned by the parser
	* @param ObjectTree $tree The parser's tree
	* @return array Data parsed
	*/
	private function processTree(ObjectTree $tree){
		$results = array();
		
		return $results;
	}
}