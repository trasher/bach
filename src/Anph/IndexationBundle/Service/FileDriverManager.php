<?php

/*
* This file is part of the Bach project.
*/

namespace Anph\IndexationBundle\Service;

use Anph\IndexationBundle\FileDriverInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;
use Anph\IndexationBundle\Entity\UniversalFileFormat;

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
    *
    * @return UniversalFileFormat the normalized file object
    */
    public function convert(SplFileInfo $fileInfo){
    	if(!array_key_exists($fileInfo->getExtension(),$this->drivers)){
			throw new \DomainException('Unsupported file format: ' . $fileInfo->getExtension());
    	}else{
    		$mapper = null;
    		//Importation configuration du driver
    		if(array_key_exists('drivers',$this->configuration)){
    			if(array_key_exists($fileInfo->getExtension(),$this->configuration['drivers'])){
    				if(array_key_exists('mapper',$this->configuration['drivers'][$fileInfo->getExtension()])){    					
    					try{
	    					$reflection = new \ReflectionClass($this->configuration['drivers'][$fileInfo->getExtension()]['mapper']);
	    					if(in_array('Anph\IndexationBundle\DriverMapperInterface',
	    								$reflection->getInterfaceNames())){
	    						$mapper = $reflection->newInstance();
	    					}
    					}catch(\RuntimeException $e){}
    				}
    			}
    		}
    		
    		$driver = $this->drivers[$fileInfo->getExtension()];
    		$result = $driver->process($fileInfo);
    		
    		if(!is_null($mapper)){
    			$result = $mapper->translate($result);
    		}
    	}
    	
    	return new UniversalFileFormat($result);
    }
    
    /**
    * Register a FileDriver into the manager
    */
    private function registerDriver(FileDriverInterface $driver){
    	if(!array_key_exists($driver->getFileFormatName(),$this->drivers)){
    		$this->drivers[$driver->getFileFormatName()] = $driver;
    	}else{
    		throw new \RuntimeException("A driver for this file format is already loaded");
    	}
    }
    
    /**
    * Perform a research of available drivers
    */
    private function searchDrivers(){
    	$finder = new Finder();
    	$finder->files()->in(__DIR__.'/../Entity/Driver')->name('*.php');
    	
    	foreach($finder as $file){ 
    		try{
	    		$reflection = new \ReflectionClass('Anph\IndexationBundle\Entity\Driver\\'.
	    											$file->getBasename('.php'));
	    	
	    		if(in_array('Anph\IndexationBundle\FileDriverInterface',
	    					$reflection->getInterfaceNames())){
	    			$this->registerDriver($reflection->newInstance());
	    		}
    		}catch(\RuntimeException $e){}
    	}
    }
    
    /**
    * Import drivers configuration file
    */
    private function importConfiguration(){
    	$this->configuration = Yaml::parse(__DIR__.'/../Resources/config/drivers.yml');   	
    }
}
