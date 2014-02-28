<?php
/**
 * Bach PMB Author entity
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
 * Bach PMB Author entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="pmb_authors")
 */
class PMBAuthor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",  length=50)
     */
    protected $type_auth;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $lastname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $codefonction;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="authors")
     * @ORM\JoinColumn(name="pmbfile_id", referencedColumnName="uniqid")
     */
    protected $pmbfile;

    /**
     * Main constructor
     *
     * @param array $data Entity data
     */
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
     * Set author type
     *
     * @param string $type_auth Author type
     *
     * @return PMBAutor
     */
    public function setTypeAuth($type_auth)
    {
        $this->type_auth = $type_auth;
        return $this;
    }

    /**
     * Get author type
     *
     * @return string
     */
    public function getTypeAuth()
    {
        return $this->type_auth;
    }

    /**
     * Set lastname
     *
     * @param string $lastname Last name
     *
     * @return PMBAuthor
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname First name
     *
     * @return PMBAuthor
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set codefonction
     *
     * @param string $codefonction Function code
     *
     * @return PMBAuthor
     */
    public function setCodefonction($codefonction)
    {
        $this->codefonction = $codefonction;
        return $this;
    }

    /**
     * Get codefonction
     *
     * @return string
     */
    public function getCodefonction()
    {
        return $this->codefonction;
    }

    /**
     * Set PMB file
     *
     * @param PMBFileFormat $pmbfile PMB file
     *
     * @return PMBAuthor
     */
    public function setPmbfile(PMBFileFormat $pmbfile)
    {
        $this->pmbfile = $pmbfile;
        return $this;
    }

    /**
     * Get PMB file
     *
     * @return PMBFileFormat
     */
    public function getPmbfile()
    {
        return $this->pmbfile;
    }
}
