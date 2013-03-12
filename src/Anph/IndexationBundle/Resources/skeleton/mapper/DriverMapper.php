<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class {{ mapper }} implements DriverMapperInterface
{
	public function translate($data){
		return $data;
	}
}