<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
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
     * @ORM\Column(type="text", length=100)
     */
    protected $fragmentid;

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
        parent::__construct($data);
    }

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
        'dao'
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
        'cDateEnd'
    );

    /**
     * Fields that will be excluded from fulltext field
     */
    public static $nonfulltext = array(
        'uniqid',
        'headerId',
        'parents',
        'archDescScopeContent',
        'fragment',
        'fragmentid',
        'cDateBegin',
        'cDateEnd',
        'dao'
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
        'cSubject'
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
        'cSubject'
    );

    /**
     * Fields types, if not string
     */
    /*public static $types = array(
        'cUnittitle' => 'text'
    );*/


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
                //FIXME: throw a warning
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

}
