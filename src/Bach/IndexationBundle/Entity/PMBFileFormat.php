<?php
/**
 * Bach PMB File Format entity
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
 * Bach PMB File Format entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBFileFormat")
 */

Class PMBFileFormat
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
    protected $titre_propre;
    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $titrepropre_auteur_different;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $titre_parallele;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $titre_complement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $codage_unimarc;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $part_of;

    /**
     * @ORM\Column(type="string", nullable=true, length=50)
     */
    protected $part_num;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $editeur;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $collection;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $num_collection;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $sous_collection;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $mention_edition;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $autre_editeur;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $isbn;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $importance_materielle;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $autres_carac_materielle;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $fomrat;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $prix;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $materiel_accompagnement;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $note_generale;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $note_content;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $extract;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $indexation_decimale;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $key_word;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $link_ressource_electronque;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $format_elect_ressource;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $statut_notice;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $url_vignette;

    /**
     * @ORM\OneToMany(targetEntity="PMBTitle", mappedBy="titleassoc", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="PMBAutors", mappedBy="autorsassoc", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $autors;

    /**
     * @ORM\OneToMany(targetEntity="PMBCategory", mappedBy="categoryassoc", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $category;
    /**
     * @ORM\OneToMany(targetEntity="PMBNoticeLink", mappedBy="noticeassoc", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $notice;
    /**
     * @ORM\OneToMany(targetEntity="PMBLanguage", mappedBy="languageassoc", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $language;

    /**
     * The constructor
     *
     * @param array $data The input data
     */
    public function __construct($data)
    {
        $this-> autors = new ArrayCollection();
        $this-> category = new ArrayCollection();
        $this-> notice = new ArrayCollection();
        $this-> language = new ArrayCollection();
        $this-> title = new ArrayCollection();
        //$this-> comments = new ArrayCollection();
        parent::__construct($data);


    }

    protected function parseData($data)
    {
        foreach ($data as $key=>$datum) {
            if (property_exists($this, $key)) {
                $this->$key = $datum;
            }
        }
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
     * Set titre_propre
     *
     * @param string $titrePropre
     * @return PMBFileFormat
     */
    public function setTitrePropre($titrePropre)
    {
        $this->titre_propre = $titrePropre;
    
        return $this;
    }

    /**
     * Get titre_propre
     *
     * @return string 
     */
    public function getTitrePropre()
    {
        return $this->titre_propre;
    }

    /**
     * Set titrepropre_auteur_different
     *
     * @param string $titrepropreAuteurDifferent
     * @return PMBFileFormat
     */
    public function setTitrepropreAuteurDifferent($titrepropreAuteurDifferent)
    {
        $this->titrepropre_auteur_different = $titrepropreAuteurDifferent;
    
        return $this;
    }

    /**
     * Get titrepropre_auteur_different
     *
     * @return string 
     */
    public function getTitrepropreAuteurDifferent()
    {
        return $this->titrepropre_auteur_different;
    }

    /**
     * Set titre_parallele
     *
     * @param string $titreParallele
     * @return PMBFileFormat
     */
    public function setTitreParallele($titreParallele)
    {
        $this->titre_parallele = $titreParallele;
    
        return $this;
    }

    /**
     * Get titre_parallele
     *
     * @return string 
     */
    public function getTitreParallele()
    {
        return $this->titre_parallele;
    }

    /**
     * Set titre_complement
     *
     * @param string $titreComplement
     * @return PMBFileFormat
     */
    public function setTitreComplement($titreComplement)
    {
        $this->titre_complement = $titreComplement;
    
        return $this;
    }

    /**
     * Get titre_complement
     *
     * @return string 
     */
    public function getTitreComplement()
    {
        return $this->titre_complement;
    }

    /**
     * Set codage_unimarc
     *
     * @param string $codageUnimarc
     * @return PMBFileFormat
     */
    public function setCodageUnimarc($codageUnimarc)
    {
        $this->codage_unimarc = $codageUnimarc;
    
        return $this;
    }

    /**
     * Get codage_unimarc
     *
     * @return string 
     */
    public function getCodageUnimarc()
    {
        return $this->codage_unimarc;
    }

    /**
     * Set part_of
     *
     * @param string $partOf
     * @return PMBFileFormat
     */
    public function setPartOf($partOf)
    {
        $this->part_of = $partOf;
    
        return $this;
    }

    /**
     * Get part_of
     *
     * @return string 
     */
    public function getPartOf()
    {
        return $this->part_of;
    }

    /**
     * Set part_num
     *
     * @param string $partNum
     * @return PMBFileFormat
     */
    public function setPartNum($partNum)
    {
        $this->part_num = $partNum;
    
        return $this;
    }

    /**
     * Get part_num
     *
     * @return string 
     */
    public function getPartNum()
    {
        return $this->part_num;
    }

    /**
     * Set editeur
     *
     * @param string $editeur
     * @return PMBFileFormat
     */
    public function setEditeur($editeur)
    {
        $this->editeur = $editeur;
    
        return $this;
    }

    /**
     * Get editeur
     *
     * @return string 
     */
    public function getEditeur()
    {
        return $this->editeur;
    }

    /**
     * Set collection
     *
     * @param string $collection
     * @return PMBFileFormat
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    
        return $this;
    }

    /**
     * Get collection
     *
     * @return string 
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set num_collection
     *
     * @param string $numCollection
     * @return PMBFileFormat
     */
    public function setNumCollection($numCollection)
    {
        $this->num_collection = $numCollection;
    
        return $this;
    }

    /**
     * Get num_collection
     *
     * @return string 
     */
    public function getNumCollection()
    {
        return $this->num_collection;
    }

    /**
     * Set sous_collection
     *
     * @param string $sousCollection
     * @return PMBFileFormat
     */
    public function setSousCollection($sousCollection)
    {
        $this->sous_collection = $sousCollection;
    
        return $this;
    }

    /**
     * Get sous_collection
     *
     * @return string 
     */
    public function getSousCollection()
    {
        return $this->sous_collection;
    }

    /**
     * Set year
     *
     * @param \DateTime $year
     * @return PMBFileFormat
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return \DateTime 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set mention_edition
     *
     * @param string $mentionEdition
     * @return PMBFileFormat
     */
    public function setMentionEdition($mentionEdition)
    {
        $this->mention_edition = $mentionEdition;
    
        return $this;
    }

    /**
     * Get mention_edition
     *
     * @return string 
     */
    public function getMentionEdition()
    {
        return $this->mention_edition;
    }

    /**
     * Set autre_editeur
     *
     * @param string $autreEditeur
     * @return PMBFileFormat
     */
    public function setAutreEditeur($autreEditeur)
    {
        $this->autre_editeur = $autreEditeur;
    
        return $this;
    }

    /**
     * Get autre_editeur
     *
     * @return string 
     */
    public function getAutreEditeur()
    {
        return $this->autre_editeur;
    }

    /**
     * Set isbn
     *
     * @param string $isbn
     * @return PMBFileFormat
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    
        return $this;
    }

    /**
     * Get isbn
     *
     * @return string 
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set importance_materielle
     *
     * @param string $importanceMaterielle
     * @return PMBFileFormat
     */
    public function setImportanceMaterielle($importanceMaterielle)
    {
        $this->importance_materielle = $importanceMaterielle;
    
        return $this;
    }

    /**
     * Get importance_materielle
     *
     * @return string 
     */
    public function getImportanceMaterielle()
    {
        return $this->importance_materielle;
    }

    /**
     * Set autres_carac_materielle
     *
     * @param string $autresCaracMaterielle
     * @return PMBFileFormat
     */
    public function setAutresCaracMaterielle($autresCaracMaterielle)
    {
        $this->autres_carac_materielle = $autresCaracMaterielle;
    
        return $this;
    }

    /**
     * Get autres_carac_materielle
     *
     * @return string 
     */
    public function getAutresCaracMaterielle()
    {
        return $this->autres_carac_materielle;
    }

    /**
     * Set fomrat
     *
     * @param string $fomrat
     * @return PMBFileFormat
     */
    public function setFomrat($fomrat)
    {
        $this->fomrat = $fomrat;
    
        return $this;
    }

    /**
     * Get fomrat
     *
     * @return string 
     */
    public function getFomrat()
    {
        return $this->fomrat;
    }

    /**
     * Set prix
     *
     * @param string $prix
     * @return PMBFileFormat
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    
        return $this;
    }

    /**
     * Get prix
     *
     * @return string 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set materiel_accompagnement
     *
     * @param string $materielAccompagnement
     * @return PMBFileFormat
     */
    public function setMaterielAccompagnement($materielAccompagnement)
    {
        $this->materiel_accompagnement = $materielAccompagnement;
    
        return $this;
    }

    /**
     * Get materiel_accompagnement
     *
     * @return string 
     */
    public function getMaterielAccompagnement()
    {
        return $this->materiel_accompagnement;
    }

    /**
     * Set note_generale
     *
     * @param string $noteGenerale
     * @return PMBFileFormat
     */
    public function setNoteGenerale($noteGenerale)
    {
        $this->note_generale = $noteGenerale;
    
        return $this;
    }

    /**
     * Get note_generale
     *
     * @return string 
     */
    public function getNoteGenerale()
    {
        return $this->note_generale;
    }

    /**
     * Set note_content
     *
     * @param string $noteContent
     * @return PMBFileFormat
     */
    public function setNoteContent($noteContent)
    {
        $this->note_content = $noteContent;
    
        return $this;
    }

    /**
     * Get note_content
     *
     * @return string 
     */
    public function getNoteContent()
    {
        return $this->note_content;
    }

    /**
     * Set extract
     *
     * @param string $extract
     * @return PMBFileFormat
     */
    public function setExtract($extract)
    {
        $this->extract = $extract;
    
        return $this;
    }

    /**
     * Get extract
     *
     * @return string 
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     * Set indexation_decimale
     *
     * @param string $indexationDecimale
     * @return PMBFileFormat
     */
    public function setIndexationDecimale($indexationDecimale)
    {
        $this->indexation_decimale = $indexationDecimale;
    
        return $this;
    }

    /**
     * Get indexation_decimale
     *
     * @return string 
     */
    public function getIndexationDecimale()
    {
        return $this->indexation_decimale;
    }

    /**
     * Set key_word
     *
     * @param string $keyWord
     * @return PMBFileFormat
     */
    public function setKeyWord($keyWord)
    {
        $this->key_word = $keyWord;
    
        return $this;
    }

    /**
     * Get key_word
     *
     * @return string 
     */
    public function getKeyWord()
    {
        return $this->key_word;
    }

    /**
     * Set link_ressource_electronque
     *
     * @param string $linkRessourceElectronque
     * @return PMBFileFormat
     */
    public function setLinkRessourceElectronque($linkRessourceElectronque)
    {
        $this->link_ressource_electronque = $linkRessourceElectronque;
    
        return $this;
    }

    /**
     * Get link_ressource_electronque
     *
     * @return string 
     */
    public function getLinkRessourceElectronque()
    {
        return $this->link_ressource_electronque;
    }

    /**
     * Set format_elect_ressource
     *
     * @param string $formatElectRessource
     * @return PMBFileFormat
     */
    public function setFormatElectRessource($formatElectRessource)
    {
        $this->format_elect_ressource = $formatElectRessource;
    
        return $this;
    }

    /**
     * Get format_elect_ressource
     *
     * @return string 
     */
    public function getFormatElectRessource()
    {
        return $this->format_elect_ressource;
    }

    /**
     * Set statut_notice
     *
     * @param string $statutNotice
     * @return PMBFileFormat
     */
    public function setStatutNotice($statutNotice)
    {
        $this->statut_notice = $statutNotice;
    
        return $this;
    }

    /**
     * Get statut_notice
     *
     * @return string 
     */
    public function getStatutNotice()
    {
        return $this->statut_notice;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return PMBFileFormat
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

    /**
     * Set url_vignette
     *
     * @param string $urlVignette
     * @return PMBFileFormat
     */
    public function setUrlVignette($urlVignette)
    {
        $this->url_vignette = $urlVignette;
    
        return $this;
    }

    /**
     * Get url_vignette
     *
     * @return string 
     */
    public function getUrlVignette()
    {
        return $this->url_vignette;
    }

    /**
     * Add title
     *
     * @param \Bach\IndexationBundle\Entity\PMBTitle $title
     * @return PMBFileFormat
     */
    public function addTitle(\Bach\IndexationBundle\Entity\PMBTitle $title)
    {
        $this->title[] = $title;
    
        return $this;
    }

    /**
     * Remove title
     *
     * @param \Bach\IndexationBundle\Entity\PMBTitle $title
     */
    public function removeTitle(\Bach\IndexationBundle\Entity\PMBTitle $title)
    {
        $this->title->removeElement($title);
    }

    /**
     * Get title
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add autors
     *
     * @param \Bach\IndexationBundle\Entity\PMBAutors $autors
     * @return PMBFileFormat
     */
    public function addAutor(\Bach\IndexationBundle\Entity\PMBAutors $autors)
    {
        $this->autors[] = $autors;
    
        return $this;
    }

    /**
     * Remove autors
     *
     * @param \Bach\IndexationBundle\Entity\PMBAutors $autors
     */
    public function removeAutor(\Bach\IndexationBundle\Entity\PMBAutors $autors)
    {
        $this->autors->removeElement($autors);
    }

    /**
     * Get autors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAutors()
    {
        return $this->autors;
    }

    /**
     * Add category
     *
     * @param \Bach\IndexationBundle\Entity\PMBCategory $category
     * @return PMBFileFormat
     */
    public function addCategory(\Bach\IndexationBundle\Entity\PMBCategory $category)
    {
        $this->category[] = $category;
    
        return $this;
    }

    /**
     * Remove category
     *
     * @param \Bach\IndexationBundle\Entity\PMBCategory $category
     */
    public function removeCategory(\Bach\IndexationBundle\Entity\PMBCategory $category)
    {
        $this->category->removeElement($category);
    }

    /**
     * Get category
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add notice
     *
     * @param \Bach\IndexationBundle\Entity\PMBNoticeLink $notice
     * @return PMBFileFormat
     */
    public function addNotice(\Bach\IndexationBundle\Entity\PMBNoticeLink $notice)
    {
        $this->notice[] = $notice;
    
        return $this;
    }

    /**
     * Remove notice
     *
     * @param \Bach\IndexationBundle\Entity\PMBNoticeLink $notice
     */
    public function removeNotice(\Bach\IndexationBundle\Entity\PMBNoticeLink $notice)
    {
        $this->notice->removeElement($notice);
    }

    /**
     * Get notice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * Add language
     *
     * @param \Bach\IndexationBundle\Entity\PMBLanguage $language
     * @return PMBFileFormat
     */
    public function addLanguage(\Bach\IndexationBundle\Entity\PMBLanguage $language)
    {
        $this->language[] = $language;
    
        return $this;
    }

    /**
     * Remove language
     *
     * @param \Bach\IndexationBundle\Entity\PMBLanguage $language
     */
    public function removeLanguage(\Bach\IndexationBundle\Entity\PMBLanguage $language)
    {
        $this->language->removeElement($language);
    }

    /**
     * Get language
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLanguage()
    {
        return $this->language;
    }
}