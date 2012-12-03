<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class EADDriverMapper implements DriverMapperInterface
{
    public function translate($data){
    	$mappedData = array();
    	$mappedData['header'] = array();
    	$mappedData['content'] = array();
    	
    	$mappedData['header']['id'] = $data['header']['eadid'][0]['value'];
    	$mappedData['header']['author'] = $data['header']['filedesc/titlestmt/author'][0]['value'];
    	$mappedData['header']['publisher'] = $data['header']['filedesc/publicationstmt/publisher'][0]['value'];
    	$mappedData['header']['address'] = $data['header']['filedesc/publicationstmt/address/addressline'][0]['value'];    	
    	$mappedData['header']['date'] = $data['header']['filedesc/publicationstmt/date'][0]['value'];  	
    	$mappedData['header']['language'] = $data['header']['profiledesc/langusage/language'][0]['attributes']['langcode'];
    	
    	return $mappedData;
    }
}
