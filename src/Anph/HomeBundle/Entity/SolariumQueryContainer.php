<?php 

namespace Anph\HomeBundle\Entity;

class SolariumQueryContainer
{
	private $fields = array();
	
	public function setField($name, $value){
		$this->fields[$name] = $value;
	}
	
	public function getField($name){
		return $this->fields[$name];
	}
	
	public function hasField($name){
		return array_key_exists($name,$this->fields);
	}
	
	public function getFields(){
		return $this->fields;
	}
}

?>