<?php 

namespace Anph\HomeBundle\Entity\Sidebar;

class OptionSidebarItemChoice
{
	private $alias;
	
	private $value;
	
	private $selected = false;
	
	public function __construct($alias, $value){
		$this->alias = $alias;
		$this->value = $value;
	}
	
	public function getAlias(){
		return $this->alias;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	public function isSelected(){
		return $this->selected;
	}
	
	public function setSelected($selected){
		$this->selected = $selected;
	}
}