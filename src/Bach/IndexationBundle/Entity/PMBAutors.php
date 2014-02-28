<?php
/**
 * Bach PMB Autors entity
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Bach PMB Autors entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBAutors")
 *
 */
 Class PMBAutors
 {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string")
     */
    protected $idpmb;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $_type;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $codefonction;

    public function __construct($data)
    {


    }

    /**
     * Get uniqid
     *
     * @return integer 
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * Set idpmb
     *
     * @param string $idpmb
     * @return PMBAutors
     */
    public function setIdpmb($idpmb)
    {
        $this->idpmb = $idpmb;
    
        return $this;
    }

    /**
     * Get idpmb
     *
     * @return string 
     */
    public function getIdpmb()
    {
        return $this->idpmb;
    }

    /**
     * Set _type
     *
     * @param string $type
     * @return PMBAutors
     */
    public function setType($type)
    {
        $this->_type = $type;
    
        return $this;
    }

    /**
     * Get _type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return PMBAutors
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return PMBAutors
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set codefonction
     *
     * @param string $codefonction
     * @return PMBAutors
     */
    public function setCodefonction($codefonction)
    {
        $this->codefonction = $codefonction;
    
        return $this;
    }

    /**
     * Get codefonction
     *
     * @return string 
     */
    public function getCodefonction()
    {
        return $this->codefonction;
    }
}