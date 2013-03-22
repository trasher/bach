<?php

/*
* This file is part of the bach project.
*/

namespace Anph\IndexationBundle\Entity;

/**
* FileDriver class
*
* @author Anaphore PI Team
*/
abstract class FileDriver
{
	protected $configuration = array();
	
	/**
	* The constructor
	* @param array $configuration The driver's configuration
	*/
    public function __construct($configuration = array()){
    	$this->configuration = $configuration;
    }
	
    /**
    * Perform the parsing of the DataBag
    * @param DataBag $bag The data
    */
	abstract public function process(DataBag $bag);
    
	/**
	* Return driver format
	* @return string $format The format of the driver
	*/
    abstract public function getFileFormatName();
}
