<?php

namespace Anph\IndexationBundle\Entity;

abstract class DataBag
{
	protected $type;
	
	protected $data;
	
	protected $fileInfo;
	
	/**
	* Type Getter
	* @return string The type of bag
	*/
	public function getType(){
		return $this->type;
	}
	
	/**
	* fileInfo Getter
	* @return SplFileInfo The spl object of the bag file
	*/
	public function getFileInfo(){
		return $this->fileInfo;
	}
	
	/**
	* Data Getter
	* @return mixed The content of bag
	*/
	public function getData(){
		return $this->data;
	}
	
	/**
	* Data Setter
	* @param mixed The content of bag
	*/
	public function setData($data){
		$this->data = $data;
	}
}