<?php
/**
 * Bach PMB Title entity
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
 * Bach PMB Title entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBTitle")
 *
 */
 Class PMBTitle
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
    protected $title_section_part;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $date_publication;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $sous_vedette_forme;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $langue;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $version;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $mention_arrangiment;

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
     * @return PMBTitle
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
     * Set title_section_part
     *
     * @param string $titleSectionPart
     * @return PMBTitle
     */
    public function setTitleSectionPart($titleSectionPart)
    {
        $this->title_section_part = $titleSectionPart;
    
        return $this;
    }

    /**
     * Get title_section_part
     *
     * @return string 
     */
    public function getTitleSectionPart()
    {
        return $this->title_section_part;
    }

    /**
     * Set date_publication
     *
     * @param string $datePublication
     * @return PMBTitle
     */
    public function setDatePublication($datePublication)
    {
        $this->date_publication = $datePublication;
    
        return $this;
    }

    /**
     * Get date_publication
     *
     * @return string 
     */
    public function getDatePublication()
    {
        return $this->date_publication;
    }

    /**
     * Set sous_vedette_forme
     *
     * @param string $sousVedetteForme
     * @return PMBTitle
     */
    public function setSousVedetteForme($sousVedetteForme)
    {
        $this->sous_vedette_forme = $sousVedetteForme;
    
        return $this;
    }

    /**
     * Get sous_vedette_forme
     *
     * @return string 
     */
    public function getSousVedetteForme()
    {
        return $this->sous_vedette_forme;
    }

    /**
     * Set langue
     *
     * @param string $langue
     * @return PMBTitle
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;
    
        return $this;
    }

    /**
     * Get langue
     *
     * @return string 
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return PMBTitle
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set mention_arrangiment
     *
     * @param string $mentionArrangiment
     * @return PMBTitle
     */
    public function setMentionArrangiment($mentionArrangiment)
    {
        $this->mention_arrangiment = $mentionArrangiment;
    
        return $this;
    }

    /**
     * Get mention_arrangiment
     *
     * @return string 
     */
    public function getMentionArrangiment()
    {
        return $this->mention_arrangiment;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return PMBTitle
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
