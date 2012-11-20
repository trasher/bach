<?php
namespace Anph\IndexationBundle\Entity;

use Anph\IndexationBundle\ObjectTreeComponentInterface;

class ObjectSheet implements ObjectTreeComponentInterface
{
	private $name;
	private $content;
		
	/**
	* The constructor
	* @param string $name The name of the sheet
	* @param mixed $content The content of the sheet
	*/
	public function __construct($name, $content)
	{
		$this->name = $name;
		$this->content = $content;
	}
	
	/**
	* Name Getter
	* @return string The name of the sheet
	*/
	public function getName()
	{
		return $this->name;
	}
	
	/**
	* Content Getter
	* @return mixed The content of the sheet
	*/
	public function getContent()
	{
		return $this->content;
	}	
}