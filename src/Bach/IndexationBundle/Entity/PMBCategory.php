<?php
/**
 * Bach PMB Category entity
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
 * Bach PMB Category entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBCategory")
 *
 */
 Class PMBCategory
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
    protected $idpmbcategory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="category")
     * @ORM\JoinColumn(name="idpmbcategory", referencedColumnName="uniqid") 
     */
    protected $categoryassoc;

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
     * Set idpmbcategory
     *
     * @param integer $idpmbcategory
     * @return PMBCategory
     */
    public function setIdpmbcategory($idpmbcategory)
    {
        $this->idpmbcategory = $idpmbcategory;
    
        return $this;
    }

    /**
     * Get idpmbcategory
     *
     * @return integer 
     */
    public function getIdpmbcategory()
    {
        return $this->idpmbcategory;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return PMBCategory
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set categoryassoc
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $categoryassoc
     * @return PMBCategory
     */
    public function setCategoryassoc(\Bach\IndexationBundle\Entity\PMBFileFormat $categoryassoc = null)
    {
        $this->categoryassoc = $categoryassoc;
    
        return $this;
    }

    /**
     * Get categoryassoc
     *
     * @return \Bach\IndexationBundle\Entity\PMBFileFormat 
     */
    public function getCategoryassoc()
    {
        return $this->categoryassoc;
    }
}