<?php

namespace Anph\IndexationBundle\Entity;

use Symfony\Component\Yaml\Yaml;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="UniversalFileFormat")
*/
class UniversalFileFormat
{
	private $configuration = array();
	
	/**
	* @ORM\Id
	* @ORM\Column(type="string", length=100)
	*/
	private $headerId;
	
	/**
	* @ORM\Column(type="string", length=100)
	*/
	private $headerAuthor;
	
	/**
	* @ORM\Column(type="date")
	*/
	private $headerDate;
	
	/**
	* @ORM\Column(type="string", length=100)
	*/
	private $headerPublisher;
	
	/**
	* @ORM\Column(type="text")
	*/
	private $headerAddress;
	
	/**
	* @ORM\Column(type="string", length=3)
	*/
	private $headerLanguage;
	
	/**
	* The constructor
	* @param array $data The input data
	*/
    public function __construct($data)
    {
    	$this->importConfiguration();
    	$this->parseData($data,$this->configuration);
    }
    
    public function __call($function,$args)
    {
    	if ( strlen($function) > 3 ) {
    		$prefix = substr($function, 0, 3);
			$name = substr($function,3);
    		$property = strtolower($name[0]).substr($name,1);
    		
    		if ($prefix == "get") {	
    			return $this->$property;
    		} elseif ($prefix == "set") {
    			$this->$property = $args[0];
    		}
    	}
    }
    
    private function parseData($data, $configuration, $keys = array())
    {
    	foreach ($configuration as $key=>$config) {
    		if (is_array($config)) {
    			if (array_key_exists($key,$data)) {
    				$this->parseData($data[$key],$config,array_merge($keys,array($key)));
    			}
    		} else {
    			$method = "set".implode(array_map('ucfirst',$keys)).ucfirst($config);
    			if (array_key_exists($config,$data)) {
    				$this->$method($data[$config]);
    			} else {
    				$this->$method(null);
    			}
    		}    		
    	}
    }
    
    /**
    * Import universalff configuration file
    */
    private function importConfiguration()
    {
    	$this->configuration = Yaml::parse(__DIR__.'/../Resources/config/universal.yml');
    	$this->configuration = $this->configuration['fields'];
    }
}
