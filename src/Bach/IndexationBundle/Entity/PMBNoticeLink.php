<?php
/**
 * Bach PMB Notice Link entity
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
 * Bach PMB Notice Link entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBNoticeLink")
 *
 */
 Class PMBNoticeLink
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
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $notice;

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
     * @return PMBNoticeLink
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
     * Set type
     *
     * @param string $type
     * @return PMBNoticeLink
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set notice
     *
     * @param string $notice
     * @return PMBNoticeLink
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    
        return $this;
    }

    /**
     * Get notice
     *
     * @return string 
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return PMBNoticeLink
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