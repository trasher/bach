<?php

namespace Bach\IndexationBundle\Entity\Mapper;

use Bach\IndexationBundle\DriverMapperInterface;

class {{ mapper }} implements DriverMapperInterface
{
	public function translate($data){
		return $data;
	}
}