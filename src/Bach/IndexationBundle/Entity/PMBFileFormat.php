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
     * Fields that will be excluded from fulltext field
     */
    public static $nonfulltext = array(
        'uniqid',
        'idNotice',
        'year'
    );

    /**
     * Fields included in spell field
     */
    public static $spellers = array(
        'titre_propre',
        'indexation_decimale',
        'editeur',
        'collection'
    );

    /**
     * Fields included in suggestions field
     */
    public static $suggesters = array(
        'titre_propre',
        'indexation_decimale',
        'editeur',
        'collection'
    );

    /**
     * Fields types, if not string
     */
    public static $types = array(
        'titre_propre'           => 'text',
        'indexation_decimale'    => 'text',
        'editeur'                => 'text',
        'collection'             => 'text',
        'fulltext'               => 'text'
    );

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
                            if ( $this->$key === null || $this->$key->format('Y') !== $year->format('Y') ) {
                                $this->onPropertyChanged($key, $this->$key, $year);
                                $this->$key = $year;
                            }
                        } catch ( \Exception $e ) {
                        }
                    } else if ( $this->$key !== $value[0]['value'] ) {
                            $this->onPropertyChanged($key, $this->$key, $value[0]['value']);
                            $this->$key = $value[0]['value'];
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
        //check for removal
        foreach ( $this->authors as $author ) {
            $found = false;
            foreach ($data as $entry) {
                if ( $author->getName() === $entry['value']
                    && $author->getTypeAuth() === $entry['attributes']['type']
                    && $author->getFunction() === PMBAuthor::convertCodeFunction($entry['attributes']['function'])
                ) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->removeAutor($author);
                $this->removed[] = $author;
                $has_changed = true;
            }
        }
        //check for new
        foreach ( $data as $entry ) {
            $unique = true;
            foreach ( $this->authors as $i ) {
                if ( $i->getName() == $entry['value']
                    && $i->getTypeAuth() == $entry['attributes']['type']
                    && $i->getFunction() == PMBAuthor::convertCodeFunction($entry['attributes']['function'])
                ) {
                    $unique = false;
                    break;
                }
            }

            if ( $unique === true ) {
                $newauthor = new PMBAuthor(
                    $entry['attributes']['type'],
                    $entry['value'],
                    $entry['attributes']['function'],
                    $this
                );
                $this->addAuthor($newauthor);
                $has_changed = true;
            }
        }

        //notify if something has changed
        if ( $has_changed ) {
            $this->onPropertyChanged('authors', $authors, $this->authors);
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
        //check for removal
        foreach ( $this->category as $category ) {
            $found = false;
            foreach ($data as $value) {
                if ( $category->getCategory() === $value['value']) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->removeCategory($category);
                $this->removed[] = $category;
                $has_changed = true;
            }
        }
        //check for new
        foreach ( $data as $value ) {
            $unique = true;
            foreach ( $this->category as $i ) {
                if ( $i->getCategory() == $value['value'] ) {
                    $unique = false;
                    break;
                }
            }

            if ( $unique === true ) {
                $result = new PMBCategory($value['value'], $this);
                $this->addCategory($result);
                $has_changed = true;
            }
        }

        //notify if something has changed
        if ( $has_changed ) {
            $this->onPropertyChanged('category', $category, $this->category);
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

        //check for removal
        foreach ( $this->language as $thelanguage ) {
            $found = false;
            foreach ($data as $value) {
                if ( $thelanguage->getContent() === $value['value']
                    && $thelanguage->getType() === $value['attributes']['type']
                ) {
                    $found = true;
                    break;
                }
            }
            if ( !$found ) {
                $this->removeLanguage($thelanguage);
                $this->removed[] = $thelanguage;
                $has_changed = true;
            }
        }
        //check for new
        foreach ( $data as $value ) {
            $unique = true;
            foreach ( $this->language as $i ) {
                if ( $i->getContent() == $value['value']
                    && $i->getType() == $value['attributes']['type']
                ) {
                    $unique = false;
                    break;
                }
            }

            if ( $unique === true ) {
                $result = new PMBLanguage($value['attributes']['type'], $value['value'], $this);
                $this->addLanguage($result);
                $has_changed = true;
            }
        }

        //notify if something has changed
        if ( $has_changed ) {
            $this->onPropertyChanged('language', $language, $this->language);
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
        if ( $this->titre_propre !== $titre_propre ) {
            $this->onPropertyChanged(
                'titre_propre',
                $this->titre_propre,
                $titre_propre
            );
            $this->titre_propre = $titre_propre;
        }
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
        if ( $this->titrepropre_auteur_different !== $titrepropre_auteur_different ) {
            $this->onPropertyChanged(
                'titrepropre_auteur_different',
                $this->titrepropre_auteur_different,
                $titrepropre_auteur_different
            );
            $this->titrepropre_auteur_different = $titrepropre_auteur_different;
        }
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
        if ( $this->titre_parallele !== $titre_parallele ) {
            $this->onPropertyChanged(
                'titre_parallele',
                $this->titre_parallele,
                $titre_parallele
            );
            $this->titre_parallele = $titre_parallele;
        }
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
        if ( $this->titre_complement !== $titre_complement ) {
            $this->onPropertyChanged(
                'titre_complement',
                $this->titre_complement,
                $titre_complement
            );
            $this->titre_complement = $titre_complement;
        }
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
        if ( $this->codage_unimarc !== $codage_unimarc ) {
            $this->onPropertyChanged(
                'codage_unimarc',
                $this->codage_unimarc,
                $codage_unimarc
            );
            $this->codage_unimarc = $codage_unimarc;
        }
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
        if ( $this->part_of !== $part_of ) {
            $this->onPropertyChanged(
                'part_of',
                $this->part_of,
                $part_of
            );
            $this->part_of = $part_of;
        }
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
        if ( $this->part_num !== $part_num ) {
            $this->onPropertyChanged(
                'part_num',
                $this->part_num,
                $part_num
            );
            $this->part_num = $part_num;
        }
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
        if ( $this->editeur !== $editeur ) {
            $this->onPropertyChanged(
                'editeur',
                $this->editeur,
                $editeur
            );
            $this->editeur = $editeur;
        }
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
        if ( $this->collection !== $collection ) {
            $this->onPropertyChanged(
                'collection',
                $this->collection,
                $collection
            );
            $this->collection = $collection;
        }
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
        if ( $this->num_collection !== $num_collection ) {
            $this->onPropertyChanged(
                'num_collection',
                $this->num_collection,
                $num_collection
            );
            $this->num_collection = $num_collection;
        }
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
        if ( $this->sous_collection !== $sous_collection ) {
            $this->onPropertyChanged(
                'sous_collection',
                $this->sous_collection,
                $sous_collection
            );
            $this->sous_collection = $sous_collection;
        }
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
        if ( $this->year !== $year ) {
            $this->onPropertyChanged(
                'year',
                $this->year,
                $year
            );
            $this->year = $year;
        }
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
        if ( $this->mention_edition !== $mention_edition ) {
            $this->onPropertyChanged(
                'mention_edition',
                $this->mention_edition,
                $mention_edition
            );
            $this->mention_edition = $mention_edition;
        }
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
        if ( $this->autre_editeur !== $autre_editeur ) {
            $this->onPropertyChanged(
                'autre_editeur',
                $this->autre_editeur,
                $autre_editeur
            );
            $this->autre_editeur = $autre_editeur;
        }
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
        if ( $this->isbn !== $isbn ) {
            $this->onPropertyChanged(
                'isbn',
                $this->isbn,
                $isbn
            );
            $this->isbn = $isbn;
        }
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
        if ( $this->importance_materielle !== $importance_materielle ) {
            $this->onPropertyChanged(
                'importance_materielle',
                $this->importance_materielle,
                $importance_materielle
            );
            $this->importance_materielle = $importance_materielle;
        }
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
        if ( $this->autresCaracMaterielle !== $autresCaracMaterielle ) {
            $this->onPropertyChanged(
                'autresCaracMaterielle',
                $this->autresCaracMaterielle,
                $autresCaracMaterielle
            );
            $this->autresCaracMaterielle = $autresCaracMaterielle;
        }
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
        if ( $this->format !== $format ) {
            $this->onPropertyChanged(
                'format',
                $this->format,
                $format
            );
            $this->format = $format;
        }
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
        if ( $this->prix !== $prix ) {
            $this->onPropertyChanged(
                'prix',
                $this->prix,
                $prix
            );
            $this->prix = $prix;
        }
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
        if ( $this->materiel_accompagnement !== $materiel_accompagnement ) {
            $this->onPropertyChanged(
                'materiel_accompagnement',
                $this->materiel_accompagnement,
                $materiel_accompagnement
            );
            $this->materiel_accompagnement = $materiel_accompagnement;
        }
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
        if ( $this->note_generale !== $note_generale ) {
            $this->onPropertyChanged(
                'note_generale',
                $this->note_generale,
                $note_generale
            );
            $this->note_generale = $note_generale;
        }
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
        if ( $this->note_content !== $note_content ) {
            $this->onPropertyChanged(
                'note_content',
                $this->note_content,
                $note_content
            );
            $this->note_content = $note_content;
        }
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
        if ( $this->extract !== $extract ) {
            $this->onPropertyChanged(
                'extract',
                $this->extract,
                $extract
            );
            $this->extract = $extract;
        }
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
        if ( $this->indexation_decimale !== $indexation_decimale ) {
            $this->onPropertyChanged(
                'indexation_decimale',
                $this->indexation_decimale,
                $indexation_decimale
            );
            $this->indexation_decimale = $indexation_decimale;
        }
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
        if ( $this->key_word !== $key_word ) {
            $this->onPropertyChanged(
                'key_word',
                $this->key_word,
                $key_word
            );
            $this->key_word = $key_word;
        }
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
        if ( $this->link_ressource_electronque !== $link_ressource_electronque ) {
            $this->onPropertyChanged(
                'link_ressource_electronque',
                $this->link_ressource_electronque,
                $link_ressource_electronque
            );
            $this->link_ressource_electronque = $link_ressource_electronque;
        }
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
        if ( $this->format_elect_ressource !== $format_elect_ressource ) {
            $this->onPropertyChanged(
                'format_elect_ressource',
                $this->format_elect_ressource,
                $format_elect_ressource
            );
            $this->format_elect_ressource = $format_elect_ressource;
        }
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
        if ( $this->url_vignette !== $url_vignette ) {
            $this->onPropertyChanged(
                'url_vignette',
                $this->url_vignette,
                $url_vignette
            );
            $this->url_vignette = $url_vignette;
        }
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
