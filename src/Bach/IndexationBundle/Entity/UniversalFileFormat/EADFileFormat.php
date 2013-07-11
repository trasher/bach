<?php

namespace Bach\IndexationBundle\Entity\UniversalFileFormat;

use Bach\IndexationBundle\Entity\UniversalFileFormat;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="EADUniversalFileFormat")
 */
class EADFileFormat extends UniversalFileFormat {
    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $parents;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $cUnitid;

    /**
     * @ORM\Column(type="string", nullable=true, length=250)
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
     * @ORM\Column(type="string", nullable=true, length=250)
     */
    protected $cDaoloc;

    /**
     * @ORM\OneToMany(targetEntity="EADIndexes", mappedBy="EADFileFormat", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $indexes;

    /**
     * The constructor
     *
     * @param array $data The input data
     */
    public function __construct($data)
    {
        $this->cPersnames = new ArrayCollection();
        parent::__construct($data);
    }

    private $_known_indexes = array(
        'cCorpnames',
        'cFamnames',
        'cGenreforms',
        'cGeognames',
        'cNames',
        'cPersnames',
        'cSubjects'
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
            if ( in_array($key, $this->_known_indexes) ) {
                foreach ( $datum as $index ) {
                    $this->addIndex($key, $index);
                }
                /*foreach ( $datum as $persname ) {
                    $this->addCPersname(new EADPersnames($this, $persname));
                }*/
            } elseif (property_exists($this, $key)) {
                $this->$key = $datum;
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
     * Set cDaoloc
     *
     * @param string $cDaoloc Daoloc
     *
     * @return EADFileFormat
     */
    public function setCDaoloc($cDaoloc)
    {
        $this->cDaoloc = $cDaoloc;
        return $this;
    }

    /**
     * Get cDaoloc
     *
     * @return string
     */
    public function getCDaoloc()
    {
        return $this->cDaoloc;
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
}
