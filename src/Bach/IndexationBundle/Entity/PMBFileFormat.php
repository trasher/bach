<?php
/**
 * Bach PMB File Format entity
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
 * Bach PMB File Format entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBFileFormat")
 */

class PMBFileFormat extends FileFormat
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
    protected $idNotice;
    /**
     * @ORM\Column(type="string")
     */
    protected $titre_propre;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $titrepropre_auteur_different;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $titre_parallele;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $titre_complement;

    /**
     * @ORM\Column(type="string")
     */
    protected $codage_unimarc;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $part_of;

    /**
     * @ORM\Column(type="string", nullable=true, length=50)
     */
    protected $part_num;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $editeur;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $collection;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $num_collection;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sous_collection;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $mention_edition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $autre_editeur;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $isbn;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $importance_materielle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $autres_carac_materielle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $format;

    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $indexation_decimale;

    /**
     * @ORM\Column(type="text", nullable=true, length=1000)
     */
    protected $key_word;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $link_ressource_electronque;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $format_elect_ressource;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url_vignette;

    /**
     * @ORM\OneToMany(targetEntity="PMBTitle", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="PMBAuthor", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $authors;

    /**
     * @ORM\OneToMany(targetEntity="PMBCategory", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="PMBLanguage", mappedBy="pmbfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $language;

    /**
     * The constructor
     *
     * @param array $data The input data
     */
    public function __construct($data)
    {
        $this-> authors  = new ArrayCollection();
        $this-> category = new ArrayCollection();
        $this-> language = new ArrayCollection();
        $this-> title    = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Proceed data parsing
     *
     * @param array $data Data
     *
     * @return void
     */
    protected function parseData($data)
    {

        foreach ($data as $key=>$value) {
            if ( $key === 'authors' ) {
                $this->parseAuthors($value);
            } else if ( $key === 'category' ) {
                $this->parseCategory($value);
            } else if ($key === 'language' ) {
                $this->parseLanguage($value);
            } else if ( $key == 'title' ) {
                $this->parseTitle($value);
            } else if (property_exists($this, $key)) {
                if ( isset($value[0])) {
                    if ( $key == 'year' ) {
                        try {
                            $year = new \DateTime(
                                $value[0]['value'] . '-01-01'
                            );
                            //TODO: test if value has changed
                            $this->$key = $year;
                        } catch ( \Exception $e ) {
                        }
                    } else if ($key == 'url_vignette') {
                        try {
                            $url = substr(urldecode($value[0]['value']), 25);
                            $this->$key = $url;
                        } catch ( \Exception $e ) {
                            throw new \RuntimeException(" error url encode");
                        }
                    } else {
                        if ( $this->$key !== $value[0]['value'] ) {
                            $this->onPropertyChanged($key, $this->$key, $value[0]['value']);
                            $this->$key = $value[0]['value'];
                        }
                    }
                }
            } else {
                throw new \RuntimeException("Missing property for entry " . $key);
            }
        }
    }

    /**
     * Parse authors objects from bag
     *
     * @param array $data Authors data
     *
     * @return void
     */
    protected function parseAuthors($data)
    {
        $authors = clone $this->authors;
        $has_changed = false;
        foreach ($data as $entry) {
            $author = new PMBAuthor(
                $entry['attributes']['type'],
                $entry['value'],
                $entry['attributes']['function'],
                $this
            );
            $this->addAuthor($author);
        }
    }

    /**
     * Parse category objects from bag
     *
     * @param array $data Category data
     *
     * @return void
     */
    protected function parseCategory($data)
    {
        $category = clone $this->category;
        $has_changed = false;
        foreach ($data as $value) {
            $result = new PMBCategory($data[0]['value'], $this);
            $this->addCategory($result);
        }

    }

    /**
     * Parse language objects from bag
     *
     * @param array $data Language data
     *
     * @return void
     */
    protected function parseLanguage($data)
    {
        $language = clone $this->language;
        $has_changed = false;
        foreach ($data as $value) {
            //var_dump( $data[0]['value']);
            $result = new PMBLanguage($data[0]['value'], $this);
            $this->addLanguage($result);
        }

    }
    /**
     * Parse title objects from bag
     *
     * @param array $data Title data
     *
     * @return void
     */
    protected function parseTitle($data)
    {
        $Title = clone $this->Title;
        $has_changed = false;
        foreach ($data as $value) {
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
     * @param string $titrePropre titrePropre
     *
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
     * @param string $titrepropreAuteurDifferent titrepropreAuteurDifferent
     *
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
     * @param string $titreParallele titreParallele
     *
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
     * @param string $titreComplement titreComplement
     *
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
     * @param string $codageUnimarc codageUnimarc
     *
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
     * @param string $partOf partOf
     *
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
     * @param string $partNum partNum
     *
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
     * @param string $editeur editeur
     *
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
     * @param string $collection collection
     *
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
     * @param string $numCollection numCollection
     *
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
     * @param string $sousCollection sousCollection
     *
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
     * @param \DateTime $year year
     *
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
     * @param string $mentionEdition mentionEdition
     *
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
     * @param string $autreEditeur autreEditeur
     *
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
     * @param string $isbn isbn
     *
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
     * @param string $importanceMaterielle importanceMaterielle
     *
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
     * @param string $autresCaracMaterielle autresCaracMaterielle
     *
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
     * Set format
     *
     * @param string $format format
     *
     * @return PMBFileFormat format
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set prix
     *
     * @param string $prix prix
     *
     * @return PMBFileFormat prix
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
     * @param string $materielAccompagnement materielAccompagnement
     *
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
     * @param string $noteGenerale noteGenerale
     *
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
     * @param string $noteContent noteContent
     *
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
     * @param string $extract extract
     *
     * @return PMBFileFormat extract
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
     * @param string $indexationDecimale indexationDecimale
     *
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
     * @param string $keyWord keyWord
     *
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
     * @param string $linkRessourceElectronque linkRessourceElectronque
     *
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
     * @param string $formatElectRessource formatElectRessource
     *
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
     * Set url_vignette
     *
     * @param string $urlVignette urlVignette
     *
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
     * @param \Bach\IndexationBundle\Entity\PMBTitle $title titre
     *
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
     * @param \Bach\IndexationBundle\Entity\PMBTitle $title title
     *
     * @return PMBFileFormat
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
     * Add author
     *
     * @param PMBAuthor $author author
     *
     * @return PMBFileFormat
     */
    public function addAuthor(PMBAuthor $author)
    {
        $this->authors[] = $author;
        return $this;
    }

    /**
     * Remove author
     *
     * @param PMBAuthor $author author
     *
     * @return PMBFileFormat
     */
    public function removeAutor(PMBAuthor $author)
    {
        $this->authors->removeElement($author);
    }

    /**
     * Get authors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Add category
     *
     * @param \Bach\IndexationBundle\Entity\PMBCategory $category category
     *
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
     * @param \Bach\IndexationBundle\Entity\PMBCategory $category category
     *
     * @return PMBFileFormat
     */
    public function removeCategory(PMBCategory $category)
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
     * Add language
     *
     * @param \Bach\IndexationBundle\Entity\PMBLanguage $language language
     *
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
     * @param PMBLanguage $language language
     *
     * @return PMBFileFormat
     */
    public function removeLanguage(PMBLanguage $language)
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

    /**
     * Set idNotice
     *
     * @param string $idNotice idNotice
     *
     * @return PMBFileFormat
     */
    public function setIdNotice($idNotice)
    {
        $this->idNotice = $idNotice;

        return $this;
    }

    /**
     * Get idNotice
     *
     * @return string
     */
    public function getIdNotice()
    {
        return $this->idNotice;
    }

    /**
     * Remove authors
     *
     * @param \Bach\IndexationBundle\Entity\PMBAuthor $authors authors
     *
     * @return PMBFileFormat
     */
    public function removeAuthor(\Bach\IndexationBundle\Entity\PMBAuthor $authors)
    {
        $this->authors->removeElement($authors);
    }

}
