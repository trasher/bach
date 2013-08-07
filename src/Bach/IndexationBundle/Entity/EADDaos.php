<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EADDaos
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class EADDaos
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
     * @ORM\Column(name="href", type="string", length=1000)
     */
    protected $href;

    /**
     * @var string
     *
     * @ORM\Column(name="audience", type="string", length=255, nullable=true)
     */
    protected $audience;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=true)
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="EADFileFormat", inversedBy="daos")
     * @ORM\JoinColumn(name="eadfile_id", referencedColumnName="uniqid")
     */
    protected $eadfile;

    /**
      * The constructor
      *
      * @param EADFileFormat $ead  EAD document referenced
      * @param array         $data The input data
      */
    public function __construct($ead, $data)
    {
        $this->eadfile = $ead;

        foreach ( $data['attributes'] as $attr=>$value) {
            switch ( $attr ){
            case 'href':
            case 'audience':
            case 'title':
            case 'role':
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
     * Set href
     *
     * @param string $href Hyperlink
     *
     * @return EADDaos
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set audience
     *
     * @param string $audience Audience
     *
     * @return EADDaos
     */
    public function setAudience($audience)
    {
        $this->audience = $audience;
        return $this;
    }

    /**
     * Get audience
     *
     * @return string
     */
    public function getAudience()
    {
        return $this->audience;
    }

    /**
     * Set title
     *
     * @param string $title Title
     *
     * @return EADDaos
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
     * Set role
     *
     * @param string $role Role
     *
     * @return EADDaos
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
     * Set eadfile
     *
     * @param EADFileFormat $eadfile
     *
     * @return EADDates
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
