<?php

namespace Anph\IndexationBundle\Entity\Mapper;

use Anph\IndexationBundle\DriverMapperInterface;

class EADDriverMapper implements DriverMapperInterface
{
    public function translate($data){
    	$mappedData = array();
    	
    	if(array_key_exists("eadid", $data['header']) && count($data['header']['eadid']) > 0){
    		$mappedData['headerId'] = $data['header']['eadid'][0]['value'];
    	}
    	
    	$mappedData["headerSubtitle"] = null;
    	
    	if(array_key_exists("filedesc/titlestmt/author", $data['header'])){
    		$mappedData['headerAuthor'] = $data['header']['filedesc/titlestmt/author'][0]['value'];
    	}
    	
    	if(array_key_exists("filedesc/publicationstmt/date", $data['header'])){
    		$mappedData['headerDate'] = $data['header']['filedesc/publicationstmt/date'][0]['value'];
    	}
    	
    	if(array_key_exists("filedesc/publicationstmt/publisher", $data['header'])){
    		$mappedData['headerPublisher'] = $data['header']['filedesc/publicationstmt/publisher'][0]['value'];
    	}
    	
    	if(array_key_exists("filedesc/publicationstmt/address/addressline", $data['header'])){
    		$mappedData['headerAddress'] = $data['header']['filedesc/publicationstmt/address/addressline'][0]['value'];
    		 
    	}
	
    	if(array_key_exists("profiledesc/langusage/language", $data['header']) 
    		&& array_key_exists('langcode', $data['header']['profiledesc/langusage/language'][0]['attributes'])){
    		$mappedData['headerLanguage'] = $data['header']['profiledesc/langusage/language'][0]['attributes']['langcode'];
    	}
    	
    	if(array_key_exists("did/unitid", $data["archdesc"])){
    		$mappedData["archDescUnitId"] = $data["archdesc"]["did/unitid"][0]["value"];
    	}
    	
    	if(array_key_exists("did/unittitle", $data["archdesc"])){
    		$mappedData["archDescUnitTitle"] = $data["archdesc"]["did/unittitle"][0]["value"];
    	}
    	
    	if(array_key_exists("did/unitdate", $data["archdesc"])){
    		$mappedData["archDescUnitDate"] = $data["archdesc"]["did/unitdate"][0]["value"];
    	}
    	
    	if(array_key_exists("did/repository", $data["archdesc"])){
       		$mappedData["archDescRepository"] = $data["archdesc"]["did/repository"][0]["value"];
    	}
    	
    	if(array_key_exists("did/langmaterial", $data["archdesc"])){
    		$mappedData["archDescLangMaterial"] = $data["archdesc"]["did/langmaterial"][0]["value"];
    	}
    	
    	if(array_key_exists("did/origination", $data["archdesc"])){
    		$mappedData["archDescLangOrigination"] = $data["archdesc"]["did/origination"][0]["value"];
    	}
    	
    	if(array_key_exists("acqinfo", $data["archdesc"])){
    		$mappedData["archDescAcqInfo"] = $data["archdesc"]["acqinfo"][0]["value"];
    	}
    	
    	if(array_key_exists("scopecontent", $data["archdesc"])){
    		$mappedData["archDescScopeContent"] = $data["archdesc"]["scopecontent"][0]["value"];
    	}
    	
    	if(array_key_exists("accruals", $data["archdesc"])){
    		$mappedData["archDescAccruals"] = $data["archdesc"]["accruals"][0]["value"];
    	}
    	
    	if(array_key_exists("arrangement", $data["archdesc"])){
    		$mappedData["archDescArrangement"] = $data["archdesc"]["arrangement"][0]["value"];
    	}
    	
    	if(array_key_exists("accessrestric", $data["archdesc"])){
    		$mappedData["archDescAccessRestrict"] = $data["archdesc"]["accessrestrict"][0]["value"];
    	}
    	//$mappedData["archDescLegalStatus"] = $data["archdesc"]["accessrestrict/legalstatus"][0]["value"];
    	
    	// Partie spécifique à l'ead    	
    	if(array_key_exists("parents", $data["c"])){
    		$mappedData["parents"] = implode("/",$data["c"]["parents"]);
    	}
    	
    	if(array_key_exists("did/unitid", $data["c"]) && count($data["c"]["did/unitid"]) > 0){
    		$mappedData["cUnitid"] = $data["c"]["did/unitid"][0]["value"];
    	}
    	
    	if(array_key_exists("did/unittitle", $data["c"]) && count($data["c"]["did/unittitle"]) > 0){
    		$mappedData["cUnittitle"] = $data["c"]["did/unittitle"][0]["value"];
    	}
    	
    	if(array_key_exists("scopcontent", $data["c"]) && count($data["c"]["scopcontent"]) > 0){
    		$mappedData["cScopcontent"] = $data["c"]["scopcontent"][0]["value"];
    	}
    	
    	if(array_key_exists("controlacces", $data["c"]) && count($data["c"]["controlacces"]) > 0){
    		$mappedData["cControlacces"] = $data["c"]["controlacces"][0]["value"];
    	}
    	
    	if(array_key_exists("daogrp/daoloc", $data["c"]) && count($data["c"]["daogrp/daoloc"]) > 0){
    		$mappedData["cDaoloc"] = $data["c"]["daogrp/daoloc"][0]["value"];
    	}
    	return $mappedData;
    }
}
