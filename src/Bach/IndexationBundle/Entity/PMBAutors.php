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
     * @ORM\Column(type="integer", length=10)
     */
    protected $idpmbautors;

    /**
     * @ORM\Column(type="string",  length=50)
     */
    protected $_type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $codefonction;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="autors")
     * @ORM\JoinColumn(name="idpmbautors", referencedColumnName="uniqid") 
     */
    protected $autorsassoc;

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
     * Set idpmbautors
     *
     * @param integer $idpmbautors
     * @return PMBAutors
     */
    public function setIdpmbautors($idpmbautors)
    {
        $this->idpmbautors = $idpmbautors;
    
        return $this;
    }

    /**
     * Get idpmbautors
     *
     * @return integer 
     */
    public function getIdpmbautors()
    {
        return $this->idpmbautors;
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

    /**
     * Set autorsassoc
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $autorsassoc
     * @return PMBAutors
     */
    public function setAutorsassoc(\Bach\IndexationBundle\Entity\PMBFileFormat $autorsassoc = null)
    {
        $this->autorsassoc = $autorsassoc;
    
        return $this;
    }

    /**
     * Get autorsassoc
     *
     * @return \Bach\IndexationBundle\Entity\PMBFileFormat 
     */
    public function getAutorsassoc()
    {
        return $this->autorsassoc;
    }
}