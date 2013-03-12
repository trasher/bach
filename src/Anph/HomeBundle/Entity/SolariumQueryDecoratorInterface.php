<?php 

namespace Anph\HomeBundle\Entity;

abstract class SolariumQueryDecoratorInterface
{
	protected $_targetField;
	
	public function getTargetField(){
		return $this->targetField;
	}
	
	abstract public function decorate(\Solarium_Query_Select $query, $data);
}
?>