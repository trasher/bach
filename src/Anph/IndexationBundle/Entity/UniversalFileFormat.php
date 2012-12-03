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
    
    private function parseData($data, $configuration, $keys = array())
    {
    	foreach ($configuration as $key=>$config) {
    		if (is_array($config)) {
    			$this->parseData(array($key=>$data[$key]),$config,array_merge($keys,array($key)));
    		} else {
    			$method = "set".implode(array_map('ucfirst',$keys)).ucfirst($config);
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
