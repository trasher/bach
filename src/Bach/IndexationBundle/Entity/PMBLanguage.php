<?php
/**
 * Bach PMB Language entity
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
 * Bach PMB Language entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBLanguage")
 *
 */
 Class PMBLanguage
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
    protected $idpmblanguage;
    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="language")
     * @ORM\JoinColumn(name="idpmblanguage", referencedColumnName="uniqid") 
     */
    protected $languageassoc;



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
     * Set idpmblanguage
     *
     * @param integer $idpmblanguage
     * @return PMBLanguage
     */
    public function setIdpmblanguage($idpmblanguage)
    {
        $this->idpmblanguage = $idpmblanguage;
    
        return $this;
    }

    /**
     * Get idpmblanguage
     *
     * @return integer 
     */
    public function getIdpmblanguage()
    {
        return $this->idpmblanguage;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return PMBLanguage
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
     * Set content
     *
     * @param string $content
     * @return PMBLanguage
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set languageassoc
     *
     * @param \Bach\IndexationBundle\Entity\PMBFileFormat $languageassoc
     * @return PMBLanguage
     */
    public function setLanguageassoc(\Bach\IndexationBundle\Entity\PMBFileFormat $languageassoc = null)
    {
        $this->languageassoc = $languageassoc;
    
        return $this;
    }

    /**
     * Get languageassoc
     *
     * @return \Bach\IndexationBundle\Entity\PMBFileFormat 
     */
    public function getLanguageassoc()
    {
        return $this->languageassoc;
    }
}