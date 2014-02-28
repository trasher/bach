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
     * @ORM\Column(type="integer", length=10)
     */
    protected $idpmbnotice;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $notice;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="notice")
     * @ORM\JoinColumn(name="idpmbnotice", referencedColumnName="uniqid") 
     */
    protected $noticeassoc;



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
     * Set idpmbnotice
     *
     * @param integer $idpmbnotice
     * @return PMBNoticeLink
     */
    public function setIdpmbnotice($idpmbnotice)
    {
        $this->idpmbnotice = $idpmbnotice;
    
        return $this;
    }

    /**
     * Get idpmbnotice
     *
     * @return integer 
     */
    public function getIdpmbnotice()
    {
        return $this->idpmbnotice;
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
     * Set noticeassoc
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $noticeassoc
     * @return PMBNoticeLink
     */
    public function setNoticeassoc(\Bach\IndexationBundle\Entity\PMBFileFormat $noticeassoc = null)
    {
        $this->noticeassoc = $noticeassoc;
    
        return $this;
    }

    /**
     * Get noticeassoc
     *
     * @return \Bach\IndexationBundle\Entity\PMBFileFormat 
     */
    public function getNoticeassoc()
    {
        return $this->noticeassoc;
    }
}