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

    /**
     * Set headerId
     *
     * @param string $headerId
     * @return UniversalFileFormat
     */
    public function setHeaderId($headerId)
    {
        $this->headerId = $headerId;
    
        return $this;
    }

    /**
     * Get headerId
     *
     * @return string 
     */
    public function getHeaderId()
    {
        return $this->headerId;
    }

    /**
     * Set headerAuthor
     *
     * @param string $headerAuthor
     * @return UniversalFileFormat
     */
    public function setHeaderAuthor($headerAuthor)
    {
        $this->headerAuthor = $headerAuthor;
    
        return $this;
    }

    /**
     * Get headerAuthor
     *
     * @return string 
     */
    public function getHeaderAuthor()
    {
        return $this->headerAuthor;
    }

    /**
     * Set headerDate
     *
     * @param \DateTime $headerDate
     * @return UniversalFileFormat
     */
    public function setHeaderDate($headerDate)
    {
        $this->headerDate = $headerDate;
    
        return $this;
    }

    /**
     * Get headerDate
     *
     * @return \DateTime 
     */
    public function getHeaderDate()
    {
        return $this->headerDate;
    }

    /**
     * Set headerPublisher
     *
     * @param string $headerPublisher
     * @return UniversalFileFormat
     */
    public function setHeaderPublisher($headerPublisher)
    {
        $this->headerPublisher = $headerPublisher;
    
        return $this;
    }

    /**
     * Get headerPublisher
     *
     * @return string 
     */
    public function getHeaderPublisher()
    {
        return $this->headerPublisher;
    }

    /**
     * Set headerAddress
     *
     * @param string $headerAddress
     * @return UniversalFileFormat
     */
    public function setHeaderAddress($headerAddress)
    {
        $this->headerAddress = $headerAddress;
    
        return $this;
    }

    /**
     * Get headerAddress
     *
     * @return string 
     */
    public function getHeaderAddress()
    {
        return $this->headerAddress;
    }

    /**
     * Set headerLanguage
     *
     * @param string $headerLanguage
     * @return UniversalFileFormat
     */
    public function setHeaderLanguage($headerLanguage)
    {
        $this->headerLanguage = $headerLanguage;
    
        return $this;
    }

    /**
     * Get headerLanguage
     *
     * @return string 
     */
    public function getHeaderLanguage()
    {
        return $this->headerLanguage;
    }
}