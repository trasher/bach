<?php

namespace Anph\HomeBundle\Entity\SolariumQueryDecorator;

use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

class PagerDecorator extends SolariumQueryDecoratorAbstract
{
	protected $_targetField = "pager";
	
	public function decorate(\Solarium_Query_Select $query, $data){
		$query->setStart($data["start"])->setRows($data["offset"]);
	}
}