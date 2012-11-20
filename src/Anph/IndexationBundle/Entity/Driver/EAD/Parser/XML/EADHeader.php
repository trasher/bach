<?php

namespace Anph\IndexationBundle\Entity\Driver\EAD\Parser\XML;

class EADHeader
{
	private $xpath;
	private $values = array();
	
	public function __construct(\DOMXPath $xpath, \DOMNode $headerNode, $fields){
		$this->xpath = $xpath;
		$this->parse($headerNode, $fields);
	}
	
	public function __get($name){
		if(array_key_exists(strtolower($name),$this->values)){
			return $this->values[strtolower($name)];
		}else{
			return null;
		}
	}
	
	public function getValues(){
		return $this->values;
	}
	
	private function parse(\DOMNode $headerNode,$fields){
		foreach($fields as $field){
			$nodes = $this->xpath->query($field,$headerNode);
			$this->values[$field] = array();
			if($nodes->length > 0){
				foreach($nodes as $key=>$node){
					$this->values[$field][] = $node->nodeValue;
				}
			}
		}
	}
}