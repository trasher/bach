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
     * @ORM\Column(type="string")
     */
    protected $idpmb;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $category;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $comment;

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
     * @return PMBCategory
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
     * Set comment
     *
     * @param string $comment
     * @return PMBCategory
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }
}