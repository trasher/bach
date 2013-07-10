<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity("filename")
 */
class ArchFileIntegrationTask
{	
	/**
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	private $taskId;
	
	/**
	* @ORM\Column(type="string", length=200)
	*/
	private $filename;
	
	/**
	 * @ORM\Column(type="string", length=200, unique=true)
	 */
	private $path;
	
	/**
	* @ORM\Column(type="string", length=30)
	*/
	private $format;
	
	/**
	* @ORM\Column(type="string", length=200, nullable=true)
	*/
	private $preprocessor;
	
	/**
	* @ORM\Column(type="integer", length=1)
	*/
	private $status;
	
	public function __construct($filename, $path, $format)
	{
		$this->filename = $filename;
		$this->path = $path;
		$this->format = $format;
		$this->status = 0;
	}

    /**
     * Get taskId
     *
     * @return integer 
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return ArchFileIntegrationTask
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Set path
     *
     * @param string $path
     * @return ArchFileIntegrationTask
     */
    public function setPath($path)
    {
    	$this->path = $path;
    
    	return $this;
    }
    
    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
    	return $this->path;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return ArchFileIntegrationTask
     */
    public function setFormat($format)
    {
        $this->format = $format;
    
        return $this;
    }

    /**
     * Get format
     *
     * @return string 
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
    * Set preprocessor
    *
    * @param string $preprocessor
    * @return ArchFileIntegrationTask
    */
    public function setPreprocessor($preprocessor)
    {
    	$this->preprocessor = $preprocessor;
    
    	return $this;
    }
    
    /**
     * Get preprocessor
     *
     * @return string
     */
    public function getPreprocessor()
    {
    	return $this->preprocessor;
    }
    
    /**
     * Set status
     *
     * @param integer $status
     * @return ArchFileIntegrationTask
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}