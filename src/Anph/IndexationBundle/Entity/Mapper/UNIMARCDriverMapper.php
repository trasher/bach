<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class UNIMARCDriverMapper implements DriverMapperInterface
{
	public function translate($data){
		// $data est un objet Notice Unimarc
		$mappedData = array();
		$sticks = $data->getSticks();
		
		foreach( $sticks as $stick){
			if($stick->getRef() == '001') {
				$mappedData['headerId'] = $stick->getArea()->getContent();
			}
			else if($stick->getRef() == '100') {
				$subAreas = $stick->getArea()->getSubAreas();
				
				foreach($subAreas as $subArea){
					if($subArea->getRef() == "a"){
						$ur = $subArea->getContent();
						$mappedData['headerDate'] = substr($ur, 0,7);
					}
				}	
				
			}
			else if($stick->getRef() == '101') {
				
			}
		}
		
		
		
		
		return $mappedData;
	}
	
	
}