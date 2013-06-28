<?php

namespace Anph\IndexationBundle\Entity\UniversalFileFormat;
use Anph\IndexationBundle\Entity\UniversalFileFormat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="EADUniversalFileFormat")
 */
class EADFileFormat extends UniversalFileFormat {
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=100)
	 */
	protected $parents;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=100)
	 */
	protected $cUnitid;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=250)
	 */
	protected $cUnittitle;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $cScopcontent; 
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=100)
	 */
	protected $cControlacces;
	
	/**
	 * @ORM\Column(type="string", nullable=true, length=250)
	 */
	protected $cDaoloc;
   
    /**
     * Set parents
     *
     * @param string $parents
     * @return EADFileFormat
     */
    public function setParents($parents)
    {
        $this->parents = $parents;
    
        return $this;
    }

    /**
     * Get parents
     *
     * @return string 
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Set cUnitid
     *
     * @param string $cUnitid
     * @return EADFileFormat
     */
    public function setCUnitid($cUnitid)
    {
        $this->cUnitid = $cUnitid;
    
        return $this;
    }

    /**
     * Get cUnitid
     *
     * @return string 
     */
    public function getCUnitid()
    {
        return $this->cUnitid;
    }

    /**
     * Set cUnittitle
     *
     * @param string $cUnittitle
     * @return EADFileFormat
     */
    public function setCUnittitle($cUnittitle)
    {
        $this->cUnittitle = $cUnittitle;
    
        return $this;
    }

    /**
     * Get cUnittitle
     *
     * @return string 
     */
    public function getCUnittitle()
    {
        return $this->cUnittitle;
    }

    /**
     * Set cScopcontent
     *
     * @param string $cScopcontent
     * @return EADFileFormat
     */
    public function setCScopcontent($cScopcontent)
    {
        $this->cScopcontent = $cScopcontent;
    
        return $this;
    }

    /**
     * Get cScopcontent
     *
     * @return string 
     */
    public function getCScopcontent()
    {
        return $this->cScopcontent;
    }

    /**
     * Set cControlacces
     *
     * @param string $cControlacces
     * @return EADFileFormat
     */
    public function setCControlacces($cControlacces)
    {
        $this->cControlacces = $cControlacces;
    
        return $this;
    }

    /**
     * Get cControlacces
     *
     * @return string 
     */
    public function getCControlacces()
    {
        return $this->cControlacces;
    }

    /**
     * Set cDaoloc
     *
     * @param string $cDaoloc
     * @return EADFileFormat
     */
    public function setCDaoloc($cDaoloc)
    {
        $this->cDaoloc = $cDaoloc;
    
        return $this;
    }

    /**
     * Get cDaoloc
     *
     * @return string 
     */
    public function getCDaoloc()
    {
        return $this->cDaoloc;
    }
}