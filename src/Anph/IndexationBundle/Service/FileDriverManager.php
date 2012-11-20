<?php

/*
* This file is part of the Bach project.
*/

namespace Anph\IndexationBundle\Service;

use Anph\IndexationBundle\Entity\FileDriver;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;
use Anph\IndexationBundle\Entity\UniversalFileFormat;
use Anph\IndexationBundle\Entity\DataBag;

/**
* FileDriverManager convert an input file into a UniversalFileFormat object
*
* @author Anaphore PI Team
*/
class FileDriverManager
{
    private $drivers = array();
    private $configuration = array();

    /**
    * Constructor
    */
    public function __construct()
    {
		$this->importConfiguration();
		$this->searchDrivers();
    }
    
    /**
    * Convert an input file into UniversalFileFormat object
    * @return UniversalFileFormat the normalized file object
    */
    public function convert(DataBag $bag, $format)
    {
    	if (!array_key_exists($format,$this->drivers)) {
			throw new \DomainException('Unsupported file format: ' . $format);
    	} else {
    		$mapper = null;
    		//Importation configuration du driver
    		if (array_key_exists('drivers',$this->configuration)) {
    			if (array_key_exists($format,$this->configuration['drivers'])) {
    				if (array_key_exists('mapper',$this->configuration['drivers'][$format])) {    					
    					try {
	    					$reflection = new \ReflectionClass($this->configuration['drivers'][$format]['mapper']);
	    					if (in_array('Anph\IndexationBundle\DriverMapperInterface',
	    								$reflection->getInterfaceNames())) {
	    						$mapper = $reflection->newInstance();
	    					}
    					} catch (\RuntimeException $e) {}
    				}
    			}
    		}
    		
    		$driver = $this->drivers[$format];
    		$results = $driver->process($bag);
    		if (!is_null($mapper)) {
    			$results = $mapper->translate($results);
    		}
    	}
    
    	return new UniversalFileFormat($results);
    }
    
    /**
    * Register a FileDriver into the manager
    */
    private function registerDriver(FileDriver $driver)
    {
    	if (!array_key_exists($driver->getFileFormatName(),$this->drivers)) {
    		$this->drivers[$driver->getFileFormatName()] = $driver;
    	} else {
    		throw new \RuntimeException("A driver for this file format is already loaded");
    	}
    }
    
    /**
    * Perform a research of available drivers
    */
    private function searchDrivers()
    {
    	$finder = new Finder();
    	$finder->directories()->in(__DIR__.'/../Entity/Driver')->depth('== 0');
    	
    	foreach ($finder as $file) { 
    		try {
	    		$reflection = new \ReflectionClass('Anph\IndexationBundle\Entity\Driver\\'.
	    											$file->getBasename().'\\Driver');
	    	
	    		if ('Anph\IndexationBundle\Entity\FileDriver' == $reflection->getParentClass()->getName()) {
	    			
	    			$configuration = array();
	    			if (array_key_exists('drivers',$this->configuration)) {
	    				if (array_key_exists(strtolower($file->getBasename()),$this->configuration['drivers'])) {
	    					$configuration = $this->configuration['drivers'][strtolower($file->getBasename())];
	    				}
	    			}
	    			
	    			$this->registerDriver($reflection->newInstanceArgs(array($configuration)));
	    		}
    		} catch(\RuntimeException $e) {}
    	}
    }
    
    /**
    * Import drivers configuration file
    */
    private function importConfiguration()
    {
    	$this->configuration = Yaml::parse(__DIR__.'/../Resources/config/drivers.yml');   	
    }
}
