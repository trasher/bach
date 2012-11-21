<?php

namespace Anph\IndexationBundle\Entity;

class KeyTranslator
{
	private $data = array();
	private $translations = array();
	
	public function __construct($data){
		if(!is_array($data)){
			throw new \RuntimeException("KeyTranslator should be instanciated 	with an array");
		}
		$this->data = $data;	
	}
	
	public function addTranslation($from, $to){
		$this->translations[$from] = $to;
	}
	
	public function translate(){
		return $this->recursive($this->data);
	}
	
	private function recursive($data){
		foreach($data as $key=>$value){			
			if(is_array($value)){
				$data[$key] = $this->recursive($value);
			}
			
			if(array_key_exists($key,$this->translations)){
				$data[$this->translations[$key]] = $value;
				unset($data[$key]);
			}			
		}
		
		return $data;
	}
}