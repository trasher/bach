<?php
/**
 * Bach EAD File Format entity
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Bach EAD File Format entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="EADUniversalFileFormat")
 */
class EADFileFormat extends MappedFileFormat
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $uniqid;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $parents;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $cUnitid;

    /**
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $cUnittitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $cScopcontent;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $cControlacces;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $fragment;

    /**
     * @ORM\Column(type="integer", length=10, nullable=true)
     */
    protected $elt_order;

    /**
     * @ORM\OneToMany(targetEntity="EADIndexes", mappedBy="eadfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $indexes;

    /**
     * @ORM\OneToMany(targetEntity="EADDates", mappedBy="eadfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $dates;

    /**
     * @ORM\OneToMany(targetEntity="EADDaos", mappedBy="eadfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $daos;

    /**
     * @ORM\OneToMany(targetEntity="EADParentTitle", mappedBy="eadfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $parents_titles;

    /**
     * @ORM\Column(type="text", length=100)
     */
    protected $fragmentid;

    /**
     * @ORM\ManyToOne(targetEntity="EADFileFormat")
     * @ORM\JoinColumn(name="archdesc_id", referencedColumnName="uniqid", onDelete="CASCADE")
     */
    protected $archdesc;

    /**
     * @ORM\OneToMany(targetEntity="\Bach\HomeBundle\Entity\Comment", mappedBy="eadfile", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $comments;

    /**
     * The constructor
     *
     * @param array $data The input data
     */
    public function __construct($data)
    {
        $this->indexes = new ArrayCollection();
        $this->dates = new ArrayCollection();
        $this->daos = new ArrayCollection();
        $this->parents_titles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Extra fields not in database
     */
    public static $extra_fields = array(
        'parents_titles' => 'unittitle'
    );

    public static $mapped_extra_db = array();

    /**
     * Additional fields not directly managed by the entity
     */
    public static $known_indexes = array(
        'cCorpname',
        'cFamname',
        'cGenreform',
        'cGeogname',
        'cName',
        'cPersname',
        'cSubject',
        'cDate',
        'cDateNormal',
        'cDateBegin',
        'cDateEnd',
        'dao',
        'cTitle'
    );

    /**
     * Fields that are mutlivalued
     */
    public static $multivalued = array(
        'dao',
        'cCorpname',
        'cFamname',
        'cGenreform',
        'cGeogname',
        'cName',
        'cPersname',
        'cSubject',
        'cDate',
        'cDateNormal',
        'cDateBegin',
        'cDateEnd',
        'subject_w_expanded',
        'parents_titles',
        'cTitle'
    );

    /**
     * Fields that will be excluded from fulltext field
     */
    public static $nonfulltext = array(
        'uniqid',
        'headerId',
        'parents',
        'elt_order',
        'archDescScopeContent',
        'fragment',
        'fragmentid',
        'cDateBegin',
        'cDateEnd',
        'dao',
        'subject_w_expanded'
    );

    /**
     * Fields included in spell field
     */
    public static $spellers = array(
        'c*'
    );

    /**
     * Fields included in suggestions field
     */
    public static $suggesters = array(
        'cUnittitle',
        'cCorpname',
        'cFamname',
        'cGenreform',
        'cGeogname',
        'cName',
        'cPersname',
        'cSubject',
        'cTitle'
    );

    /**
     * Descriptors fields
     */
    public static $descriptors = array(
        'cCorpname',
        'cFamname',
        'cGenreform',
        'cGeogname',
        'cName',
        'cPersname',
        'cSubject',
        'cTitle'
    );

    /**
     * Fields types, if not string
     */
    public static $types = array(
        'cUnittitle'            => 'alphaOnlySort',
        'elt_order'             => 'int',
        'subject_w_expanded'    => 'skosLabel',
        'parents_titles'        => 'text',
        'cDateBegin'            => 'date',
        'cDateEnd'              => 'date'
    );

    /**
     * String fields that must have a text version
     */
    public static $textMapped = array(
        'cUnittitle' => 'tcUnittitle'
    );

    /**
     * Fields that are not indexed
     */
    public static $nonindexed = array(
        'fragment'
    );

    /**
     * Dynamic descriptors discover
     *
     * array(
     *  db column name not null => solr function called (defined in schema.xml)
     * )
     */
    public static $dynamic_descriptors = array(
        'source'    => 'makeSourcesDynamics',
        'role'      => 'makeRolesDynamics'
    );

    /**
     * Fields that should not be used for facetting
     */
    public static $facet_excluded = array(
        '_version_',
        'fragment',
        'fragmentid',
        'fulltext',
        'parents_titles',
        'parents',
        'spell',
        'suggestions',
        'tcUnittitle',
        'uniqid',
        'cScopcontent',
        'cDateNormal',
        'cDateBegin',
        'cDateEnd',
        'headerAddress',
        'headerDate',
        'archDescOrigination',
        'subject_w_expanded',
        'archDescScopeContent'
    );

    public static $expanded_mappings = array(
        array(
            'source'        => 'cSubject',
            'dest'          => 'subject_w_expanded',
            'type'          => 'skos_w_label',
            'multivalued'   => 'true',
            'indexed'       => 'true',
            'stored'        => 'true'
        )
    );

    /**
     * Proceed data parsing
     *
     * @param array $data Data
     *
     * @return void
     */
    protected function parseData($data)
    {
        foreach ($data as $key=>$datum) {
            if ( in_array($key, self::$known_indexes) ) {
                foreach ( $datum as $index ) {
                    $this->addIndex($key, $index);
                }
            } else if ( $key === 'parents_titles' ) {
                foreach ( $datum as $d ) {
                    $this->addParentTitle($d);
                }
            } elseif (property_exists($this, $key)) {
                $this->$key = $datum;
            } elseif ($key === 'cUnitDate' || $key === 'cDate') {
                foreach ( $datum as $date ) {
                    $this->addDate($date);
                }
            } elseif ( $key == 'daolist' ) {
                foreach ( $datum as $dao ) {
                    $this->addDao($dao);
                }
            } else {
                throw new \RuntimeException(
                    __CLASS__ . ' - Key ' . $key . ' is not known!'
                );
            }
        }
    }

    /**
     * Set parents
     *
     * @param string $parents Parents
     *
     * @return EADFileFormat
     */
    public function setParents($parents)
    {
        $this->parents = $parents;
        return $this;
    }

    /**
     * Get parents
     *
     * @return string
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Set cUnitid
     *
     * @param string $cUnitid unitid
     *
     * @return EADFileFormat
     */
    public function setCUnitid($cUnitid)
    {
        $this->cUnitid = $cUnitid;
        return $this;
    }

    /**
     * Get cUnitid
     *
     * @return string
     */
    public function getCUnitid()
    {
        return $this->cUnitid;
    }

    /**
     * Set cUnittitle
     *
     * @param string $cUnittitle unittitle
     *
     * @return EADFileFormat
     */
    public function setCUnittitle($cUnittitle)
    {
        $this->cUnittitle = $cUnittitle;
        return $this;
    }

    /**
     * Get cUnittitle
     *
     * @return string
     */
    public function getCUnittitle()
    {
        return $this->cUnittitle;
    }

    /**
     * Set cScopcontent
     *
     * @param string $cScopcontent scopecontent
     *
     * @return EADFileFormat
     */
    public function setCScopcontent($cScopcontent)
    {
        $this->cScopcontent = $cScopcontent;
        return $this;
    }

    /**
     * Get cScopcontent
     *
     * @return string
     */
    public function getCScopcontent()
    {
        return $this->cScopcontent;
    }

    /**
     * Set cControlacces
     *
     * @param string $cControlacces controlaccess
     *
     * @return EADFileFormat
     */
    public function setCControlacces($cControlacces)
    {
        $this->cControlacces = $cControlacces;
        return $this;
    }

    /**
     * Get cControlacces
     *
     * @return string
     */
    public function getCControlacces()
    {
        return $this->cControlacces;
    }

    /**
     * Add index
     *
     * @param string $type  Index type
     * @param string $index Index data
     *
     * @return EADFileFormat
     */
    public function addIndex($type, $index)
    {
        $idx = new EADIndexes($this, $type, $index);
        //dedupe
        $unique = true;
        foreach ( $this->indexes as $i ) {
            if ( $i->getType() == $type and $i->getName() == $index['value'] ) {
                $unique = false;
                break;
            }
        }
        if ( $unique === true ) {
            $this->indexes[] = $idx;
        }
        return $this;
    }

    /**
     * Remove index
     *
     * @param EADIndexes $index Index
     *
     * @return void
     */
    public function removeIndex(EADIndexes $index)
    {
        $this->indexes->removeElement($index);
    }

    /**
     * Get indexes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Add date
     *
     * @param string $date Date
     *
     * @return EADFileFormat
     */
    public function addDate($date)
    {
        $this->dates[] = new EADDates($this, $date);
        return $this;
    }

    /**
     * Remove date
     *
     * @param EADDates $date Date
     *
     * @return void
     */
    public function removeDate(EADDates $date)
    {
        $this->dates->removeElement($date);
    }

    /**
     * Get dates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Add dao
     *
     * @param array $dao dao
     *
     * @return EADFileFormat
     */
    public function addDao($dao)
    {
        $this->daos[] = new EADDaos($this, $dao);
        return $this;
    }

    /**
     * Remove dao
     *
     * @param EADDaos $dao Dao
     *
     * @return void
     */
    public function removeDao(EADDaos $dao)
    {
        $this->daos->removeElement($dao);
    }

    /**
     * Get daos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDaos()
    {
        return $this->daos;
    }

    /**
     * Add parent title
     *
     * @param string $title title
     *
     * @return EADFileFormat
     */
    public function addParentTitle($title)
    {
        $this->parents_titles[] = new EADParentTitle($this, $title);
        return $this;
    }

    /**
     * Remove parent title
     *
     * @param EADParentTitle $title Title
     *
     * @return void
     */
    public function removeParentTitle(EADParentTitle $title)
    {
        $this->parents_titles->removeElement($title);
    }

    /**
     * Get parents titles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParentsTitles()
    {
        return $this->parents_titles;
    }

}
