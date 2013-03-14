<?php 

namespace Anph\HomeBundle\Entity\Sidebar;

class OptionSidebarItem
{
	private $name;
	
	private $choices;
	
	private $key;
	
	public function __construct($name, $key, $default){
		$this->name = $name;
		$this->key = $key;
		$this->default = $default;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getKey(){
		return $this->key;
	}
	
	public function getDefault(){
		return $this->default;
	}
	
	public function appendChoice(OptionSidebarItemChoice $choice){
		$this->choices[$choice->getValue()] = $choice;
		
		return $this;
	}
	
	public function getChoices(){
		return $this->choices;
	}
}