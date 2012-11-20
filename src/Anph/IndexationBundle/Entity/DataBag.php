<?php

namespace Anph\IndexationBundle\Entity;

abstract class DataBag
{
	protected $type;
	
	protected $data;
	
	/**
	* Type Getter
	* @return string The type of bag
	*/
	public function getType(){
		return $this->type;
	}
	
	/**
	* Data Getter
	* @return mixed The content of bag
	*/
	public function getData(){
		return $this->data;
	}
}