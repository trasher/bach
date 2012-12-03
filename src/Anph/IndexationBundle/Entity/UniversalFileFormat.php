<?php

namespace Anph\IndexationBundle\Entity;

use Symfony\Component\Yaml\Yaml;

class UniversalFileFormat
{
	private $configuration = array();
	
	/**
	* The constructor
	* @param array $data The input data
	*/
    public function __construct($data)
    {
    	$this->importConfiguration();
    	$this->parseData($data);
    }
    
    private function parseData($data, $keys = array())
    {
    	foreach ($this->configuration as $key=>$config) {
    		if (is_array($config)) {
    			$this->parseData($data,array_merge($keys,array($key)));
    		} else {
    			$method = "set".implode(array_map('ucfirst',$keys));
    			$this->$method($this->lookForKeys($keys,$data));
    		}
    	}
    }
    
    private function lookForKeys($keys, $data) {
    	$key = array_shift($keys);
    	
    	if ( !is_null($key) ) {
    		if (array_key_exists($key,$data) ) {
    			$this->lookForKeys($keys, $data);
    		}
    	} else {
    		return $data;
    	}
    }
    
    /**
    * Import universalff configuration file
    */
    private function importConfiguration()
    {
    	$this->configuration = Yaml::parse(__DIR__.'/../Resources/config/universalff.yml');
    	$this->configuration = $this->configuration['fields'];
    }
}
