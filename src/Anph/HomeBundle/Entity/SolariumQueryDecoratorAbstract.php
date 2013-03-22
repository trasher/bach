<?php 

namespace Anph\HomeBundle\Entity;

abstract class SolariumQueryDecoratorAbstract
{	
	public function getTargetField(){
		return $this->_targetField;
	}
	
	abstract public function decorate(\Solarium_Query_Select $query, $data);
}
?>