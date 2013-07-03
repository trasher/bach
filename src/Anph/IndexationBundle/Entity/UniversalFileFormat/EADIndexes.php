<?php

namespace Anph\IndexationBundle\Entity\UniversalFileFormat;

use Doctrine\ORM\Mapping as ORM;

/**
 * EADIndexes
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EADIndexes
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
     * @ORM\COlumn(name="type", type="string", length=20)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="normal", type="string", length=255, nullable=true)
     */
    protected $normal;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=100, nullable=true)
     */
    protected $role;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255, nullable=true)
     */
    protected $source;

    /**
     * @ORM\ManyToOne(targetEntity="EADFileFormat", inversedBy="EADIndexes")
     * @ORM\JoinColumn(name="eadfile_id", referencedColumnName="uniqid")
     */
    protected $eadfile;

    /**
      * The constructor
      *
      * @param EADFileFormat $ead  EAD document referenced
      * @param string        $type Index type (persname, geogname, etc)
      * @param array         $data The input data
      */
    public function __construct($ead, $type, $data)
    {
        $this->eadfile = $ead;
        $this->name = $data['value'];
        $this->type = $type;
        foreach ( $data['attributes'] as $attr=>$value) {
            switch ( $attr ){
            case 'role':
            case 'source':
            case 'normal':
                $this->$attr = $value;
                break;
            default:
                //FIXME: throw a warning, attribute is not mapped
            }
        }
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
     * Set type
     *
     * @param string $type Type
     *
     * @return EADIndexes
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
     * Set name
     *
     * @param string $name
     * @return EADIndexes
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set normal
     *
     * @param string $normal
     * @return EADIndexes
     */
    public function setNormal($normal)
    {
        $this->normal = $normal;
    
        return $this;
    }

    /**
     * Get normal
     *
     * @return string 
     */
    public function getNormal()
    {
        return $this->normal;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return EADIndexes
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return EADIndexes
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set eadfile
     *
     * @param \Anph\IndexationBundle\Entity\EADFileFormat $eadfile
     * @return EADIndexes
     */
    public function setEadfile(\Anph\IndexationBundle\Entity\EADFileFormat $eadfile = null)
    {
        $this->eadfile = $eadfile;
    
        return $this;
    }

    /**
     * Get eadfile
     *
     * @return \Anph\IndexationBundle\Entity\EADFileFormat 
     */
    public function getEadfile()
    {
        return $this->eadfile;
    }
}
