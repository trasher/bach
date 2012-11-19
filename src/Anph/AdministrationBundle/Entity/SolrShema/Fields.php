<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
class Fields {
	private $fieldList;
	private $dynamicFieldList;
	
	public function __construct(){
		$this->fieldList = array();
		$this->dynamicFieldList=array();
	}
	
	
	private function getFieldList(){
		return $this->fieldList;
	}
	
	private function getDynamicFieldList(){
		return $this->dynamicFieldList;
	}
	
	private function getFieldAtIndex($index){
		return $this->fieldList[$index];
	}
	
	private function getDynamicFieldAtIndex($index){
		return $this->dynamicFieldList[$index];
	}
	
	
	private function addStaticField($staticField){
		$this->fieldList=$staticField;
	}
	
	private function addDynamicField($dynamicField){
	 	$this->dynamicFieldList=$dynamicField;
	}
	
	private function resetLists(){
		$this->fieldList=NULL;
		$this->dynamicFieldList=NULL;
		$this->fieldList=array();
		$this->dynamicFieldList=array();
	}
	
	private function setStaticFieldList($newStaticFieldList){
		$this->fieldList=$newStaticFieldList;
		
	}
	
	
	
	
	
	
}
