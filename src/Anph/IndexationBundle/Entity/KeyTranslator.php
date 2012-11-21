<?php

namespace Anph\IndexationBundle\Entity;

class KeyTranslator
{
	private $data = array();
	private $translations = array();
	
	public function __construct($data){
		if (!is_array($data)) {
			throw new \RuntimeException("KeyTranslator should be instanciated 	with an array");
		}
		$this->data = $data;	
	}
	
	public function addTranslation($from, $to, $deep = 0){
		$this->translations[$from] = array(	'to'	=>	$to,
											'deep'	=>	$deep);
	}
	
	public function translate(){
		return $this->recursive($this->data, 0);
	}
	
	private function recursive($data, $deep){
		foreach($data as $key=>$value){			
			if(is_array($value)){
				$data[$key] = $this->recursive($value, $deep+1);
			}
			if (array_key_exists($key,$this->translations)) {
				if ($this->translations[$key]['deep'] == 0
					|| ($this->translations[$key]['deep'] > 0 && $this->translations[$key]['deep'] == $deep)) {
					$data[$this->translations[$key]['to']] = $value;
					unset($data[$key]);
				}		
			}	
		}
		
		return $data;
	}
}