<?php
/**
 * Bach PMB Category entity
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
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
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="pmbcategory")
 *
 */
class PMBCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="category")
     * @ORM\JoinColumn(name="pmbfile_id", referencedColumnName="uniqid")
     */
    protected $pmbfile;

     /**
     * Main constructor
     *
     * @param array $data Entity data
     */

    public function __construct($category,$pmb)
    {
        $this->pmbfile = $pmb;
        $this->category = $category;
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
     *
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
     * @param PMBFileFormat $categoryassoc
     *
     * @return PMBCategory
     */
    public function setCategoryassoc(PMBFileFormat $categoryassoc = null)
    {
        $this->categoryassoc = $categoryassoc;
        return $this;
    }

    /**
     * Get categoryassoc
     *
     * @return PMBFileFormat
     */
    public function getCategoryassoc()
    {
        return $this->categoryassoc;
    }
 }
