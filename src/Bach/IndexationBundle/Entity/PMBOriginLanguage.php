<?php
/**
 * Bach PMB Origin Language entity
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
 * Bach PMB Origin Language entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBOriginLanguage")
 *
 */
 Class PMBOriginLanguage
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
    protected $langue_originale;

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
     * @return PMBOriginLanguage
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
     * Set langue_originale
     *
     * @param string $langueOriginale
     * @return PMBOriginLanguage
     */
    public function setLangueOriginale($langueOriginale)
    {
        $this->langue_originale = $langueOriginale;
    
        return $this;
    }

    /**
     * Get langue_originale
     *
     * @return string 
     */
    public function getLangueOriginale()
    {
        return $this->langue_originale;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return PMBOriginLanguage
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
