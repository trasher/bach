<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class UNIMARCDriverMapper implements DriverMapperInterface
{
	public function translate($data){
		// $data est un objet Notice Unimarc
		$mappedData = array();
		if($data->getSticks[0] == '001') {
			$mappedData['headerId'] = $data->getSticks[0].getArea();
		}
		
		
		return $mappedData;
	}
	
	
}