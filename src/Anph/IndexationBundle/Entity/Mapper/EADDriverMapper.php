<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class EADDriverMapper implements DriverMapperInterface
{
    public function translate($data){
    	$mappedData = array();
    	
    	$mappedData['headerId'] = $data['header']['eadid'][0]['value'];
    	$mappedData['headerAuthor'] = $data['header']['filedesc/titlestmt/author'][0]['value'];
    	$mappedData['headerPublisher'] = $data['header']['filedesc/publicationstmt/publisher'][0]['value'];
    	$mappedData['headerAddress'] = $data['header']['filedesc/publicationstmt/address/addressline'][0]['value'];    	
    	$mappedData['headerDate'] = $data['header']['filedesc/publicationstmt/date'][0]['value'];  	
    	$mappedData['headerLanguage'] = $data['header']['profiledesc/langusage/language'][0]['attributes']['langcode'];
    	
    	return $mappedData;
    }
}
