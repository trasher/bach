<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class EADDriverMapper implements DriverMapperInterface
{
    public function translate($data){
    	return $data;
    }
}
