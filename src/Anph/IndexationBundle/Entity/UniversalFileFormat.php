<?php

namespace Anph\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="UniversalFileFormat")
*/
class UniversalFileFormat
{
	
	/**
	* @ORM\Id
	* @ORM\Column(type="integer", length=10)
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $uniqid;
	
	/**
	* @ORM\Column(type="string", nullable=true, length=100)
	*/
	protected $headerId;
	
	/**
	* @ORM\Column(type="string", nullable=true, length=100)
	*/
	protected $headerAuthor;
	
	/**
	* @ORM\Column(type="string", nullable=true, length=100)
	*/
	protected $headerDate;
	
	/**
	* @ORM\Column(type="string", nullable=true, length=100)
	*/
	protected $headerPublisher;
	
	/**
	* @ORM\Column(type="text", nullable=true)
	*/
	protected $headerAddress;
	
	/**
	* @ORM\Column(type="string", nullable=true, length=3)
	*/
	protected $headerLanguage;
	
	/**
	* The constructor
	* @param array $data The input data
	*/
    public function __construct($data)
    {
    	$this->parseData($data);
    }
        
    protected function parseData($data)
    {
    	foreach ($data as $key=>$datum) {
    		if (property_exists($this, $key)) {
    			$this->$key = $datum;
    		}    		
    	}
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