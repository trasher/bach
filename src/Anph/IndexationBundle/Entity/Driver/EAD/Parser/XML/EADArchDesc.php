<?php

namespace Anph\IndexationBundle\Entity\Driver\EAD\Parser\XML;

class EADArchDesc
{
	private $xpath;
	private $values = array();
	
	public function __construct(\DOMXPath $xpath, \DOMNode $archDescNode, $fields){
		$this->xpath = $xpath;
		$this->parse($archDescNode,$fields);
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
	
	private function parse(\DOMNode $archDescNode,$fields){
		$results = $this->recursiveCNodeSearch($archDescNode->getElementsByTagName('dsc')->item(0),
											$fields);
		
		$this->values = $results;
	}
	
	private function recursiveCNodeSearch(\DOMNode $rootNode, $fields){
		$results = array();
	
		$cNodes = $this->xpath->query('c',$rootNode);
	
		foreach($cNodes as $cNode){
			$results[$cNode->getAttribute('id')] = array();
	
			foreach($fields as $field){
				$nodes = $this->xpath->query($field,$cNode);
	
				if($nodes->length > 0){						
					$results[$cNode->getAttribute('id')][$field] = array();
					foreach($nodes as $key=>$node){
						$results[$cNode->getAttribute('id')][$field][] = $nodeValue;
					}
				}
			}
				
			if($this->xpath->query('c',$cNode)->length > 0){
				$results = array_merge($results,$this->recursiveCNodeSearch($cNode, $fields));
			}
		}
		return $results;
	}
}