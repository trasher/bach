<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EADParentTitle
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EADParentTitle
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="unittitle", type="string", length=1000)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="EADFileFormat", inversedBy="parents_titles")
     * @ORM\JoinColumn(name="eadfile_id", referencedColumnName="uniqid")
     */
    protected $eadfile;

    /**
      * The constructor
      *
      * @param EADFileFormat $ead   EAD document referenced
      * @param string        $title Parent title
      */
    public function __construct($ead, $title)
    {
        $this->eadfile = $ead;
        $this->title = $title;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title Title
     *
     * @return EADParentTitle
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set eadfile
     *
     * @param EADFileFormat $eadfile EAD File
     *
     * @return EADIndexes
     */
    public function setEadfile(EADFileFormat $eadfile = null)
    {
        $this->eadfile = $eadfile;
        return $this;
    }

    /**
     * Get eadfile
     *
     * @return EADFileFormat
     */
    public function getEadfile()
    {
        return $this->eadfile;
    }
}
