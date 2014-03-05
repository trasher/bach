<?php
/**
 * Bach mapped file format superclass
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
use Bach\IndexationBundle\Entity\Document;

/**
 * Bach mapped file format superclass
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\MappedSuperclass
 */
class MappedFileFormat extends FileFormat
{
    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $headerSubtitle;

    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerAuthor;

    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerDate;

    /**
    * @ORM\Column(type="string", nullable=true, length=100)
    */
    protected $headerPublisher;

    /**
    * @ORM\Column(type="text", nullable=true)
    */
    protected $headerAddress;

    /**
    * @ORM\Column(type="string", nullable=true, length=3)
    */
    protected $headerLanguage;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $archDescUnitId;

    /**
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $archDescUnitTitle;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $archDescUnitDate;

    /**
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $archDescDimension;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $archDescRepository;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=100)
     */
    protected $archDescLangMaterial;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $archDescOrigination;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $archDescAcqInfo;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $archDescScopeContent;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $archDescAccruals;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $archDescArrangement;

    /**
     *
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    protected $archDescAccessRestrict;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $archDescLegalStatus;

    /**
     * Set headerId
     *
     * @param string $headerId Header id
     *
     * @return UniversalFileFormat
     */
    public function setHeaderId($headerId)
    {
        $this->headerId = $headerId;

        return $this;
    }

    /**
     * Get headerId
     *
     * @return string
     */
    public function getHeaderId()
    {
        return $this->headerId;
    }

    /**
     * Set headerAuthor
     *
     * @param string $headerAuthor header author
     *
     * @return UniversalFileFormat
     */
    public function setHeaderAuthor($headerAuthor)
    {
        $this->headerAuthor = $headerAuthor;

        return $this;
    }

    /**
     * Get headerAuthor
     *
     * @return string
     */
    public function getHeaderAuthor()
    {
        return $this->headerAuthor;
    }

    /**
     * Set headerDate
     *
     * @param \DateTime $headerDate Header date
     *
     * @return UniversalFileFormat
     */
    public function setHeaderDate($headerDate)
    {
        $this->headerDate = $headerDate;
        return $this;
    }

    /**
     * Get headerDate
     *
     * @return \DateTime
     */
    public function getHeaderDate()
    {
        return $this->headerDate;
    }

    /**
     * Set headerPublisher
     *
     * @param string $headerPublisher Header publisher
     *
     * @return UniversalFileFormat
     */
    public function setHeaderPublisher($headerPublisher)
    {
        $this->headerPublisher = $headerPublisher;
        return $this;
    }

    /**
     * Get headerPublisher
     *
     * @return string
     */
    public function getHeaderPublisher()
    {
        return $this->headerPublisher;
    }

    /**
     * Set headerAddress
     *
     * @param string $headerAddress Header address
     *
     * @return UniversalFileFormat
     */
    public function setHeaderAddress($headerAddress)
    {
        $this->headerAddress = $headerAddress;
        return $this;
    }

    /**
     * Get headerAddress
     *
     * @return string
     */
    public function getHeaderAddress()
    {
        return $this->headerAddress;
    }

    /**
     * Get headerSubtitle
     *
     * @return string
     */
    public function getHeaderSubtitle()
    {
        return $this->headerSubtitle;
    }

    /**
     * Set headerSubtitle
     *
     * @param string $headerSubtitle Header subtitle
     *
     * @return UniversalFileFormat
     */
    public function setHeaderSubtitle($headerSubtitle)
    {
        $this->headerSubtitle = $headerSubtitle;
        return $this;
    }

    /**
     * Set headerLanguage
     *
     * @param string $headerLanguage Header language
     *
     * @return UniversalFileFormat
     */
    public function setHeaderLanguage($headerLanguage)
    {
        $this->headerLanguage = $headerLanguage;
        return $this;
    }

    /**
     * Get headerLanguage
     *
     * @return string
     */
    public function getHeaderLanguage()
    {
        return $this->headerLanguage;
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
     * Set archDescUnitId
     *
     * @param string $archDescUnitId Arhival description unit id
     *
     * @return UniversalFileFormat
     */
    public function setArchDescUnitId($archDescUnitId)
    {
        $this->archDescUnitId = $archDescUnitId;
        return $this;
    }

    /**
     * Get archDescUnitId
     *
     * @return string
     */
    public function getArchDescUnitId()
    {
        return $this->archDescUnitId;
    }

    /**
     * Set archDescUnitTitle
     *
     * @param string $archDescUnitTitle Archival description unit title
     *
     * @return UniversalFileFormat
     */
    public function setArchDescUnitTitle($archDescUnitTitle)
    {
        $this->archDescUnitTitle = $archDescUnitTitle;
        return $this;
    }

    /**
     * Get archDescUnitTitle
     *
     * @return string
     */
    public function getArchDescUnitTitle()
    {
        return $this->archDescUnitTitle;
    }

    /**
     * Set archDescUnitDate
     *
     * @param string $archDescUnitDate Archival description unit date
     *
     * @return UniversalFileFormat
     */
    public function setArchDescUnitDate($archDescUnitDate)
    {
        $this->archDescUnitDate = $archDescUnitDate;
        return $this;
    }

    /**
     * Get archDescUnitDate
     *
     * @return string
     */
    public function getArchDescUnitDate()
    {
        return $this->archDescUnitDate;
    }

    /**
     * Set archDescDimension
     *
     * @param string $archDescDimension Archival description dimensions
     *
     * @return UniversalFileFormat
     */
    public function setArchDescDimension($archDescDimension)
    {
        $this->archDescDimension = $archDescDimension;
        return $this;
    }

    /**
     * Get archDescDimension
     *
     * @return string
     */
    public function getArchDescDimension()
    {
        return $this->archDescDimension;
    }

    /**
     * Set archDescRepository
     *
     * @param string $archDescRepository Archival description repository
     *
     * @return UniversalFileFormat
     */
    public function setArchDescRepository($archDescRepository)
    {
        $this->archDescRepository = $archDescRepository;
        return $this;
    }

    /**
     * Get archDescRepository
     *
     * @return string 
     */
    public function getArchDescRepository()
    {
        return $this->archDescRepository;
    }

    /**
     * Set archDescLangMaterial
     *
     * @param string $archDescLangMaterial Archival description lang material
     *
     * @return UniversalFileFormat
     */
    public function setArchDescLangMaterial($archDescLangMaterial)
    {
        $this->archDescLangMaterial = $archDescLangMaterial;
        return $this;
    }

    /**
     * Get archDescLangMaterial
     *
     * @return string
     */
    public function getArchDescLangMaterial()
    {
        return $this->archDescLangMaterial;
    }

    /**
     * Set archDescOrigination
     *
     * @param string $archDescOrigination Archival description origination
     *
     * @return UniversalFileFormat
     */
    public function setArchDescOrigination($archDescOrigination)
    {
        $this->archDescOrigination = $archDescOrigination;
        return $this;
    }

    /**
     * Get archDescOrigination
     *
     * @return string
     */
    public function getArchDescOrigination()
    {
        return $this->archDescOrigination;
    }

    /**
     * Set archDescAcqInfo
     *
     * @param string $archDescAcqInfo Archival description acquisition informations
     *
     * @return UniversalFileFormat
     */
    public function setArchDescAcqInfo($archDescAcqInfo)
    {
        $this->archDescAcqInfo = $archDescAcqInfo;
        return $this;
    }

    /**
     * Get archDescAcqInfo
     *
     * @return string
     */
    public function getArchDescAcqInfo()
    {
        return $this->archDescAcqInfo;
    }

    /**
     * Set archDescScopeContent
     *
     * @param string $archDescScopeContent Archival description scope content
     *
     * @return UniversalFileFormat
     */
    public function setArchDescScopeContent($archDescScopeContent)
    {
        $this->archDescScopeContent = $archDescScopeContent;
        return $this;
    }

    /**
     * Get archDescScopeContent
     *
     * @return string
     */
    public function getArchDescScopeContent()
    {
        return $this->archDescScopeContent;
    }

    /**
     * Set archDescAccruals
     *
     * @param string $archDescAccruals Archival description accurals
     *
     * @return UniversalFileFormat
     */
    public function setArchDescAccruals($archDescAccruals)
    {
        $this->archDescAccruals = $archDescAccruals;
        return $this;
    }

    /**
     * Get archDescAccruals
     *
     * @return string
     */
    public function getArchDescAccruals()
    {
        return $this->archDescAccruals;
    }

    /**
     * Set archDescArrangement
     *
     * @param string $archDescArrangement Archival description arrangement
     *
     * @return UniversalFileFormat
     */
    public function setArchDescArrangement($archDescArrangement)
    {
        $this->archDescArrangement = $archDescArrangement;
        return $this;
    }

    /**
     * Get archDescArrangement
     *
     * @return string
     */
    public function getArchDescArrangement()
    {
        return $this->archDescArrangement;
    }

    /**
     * Set archDescAccessRestrict
     *
     * @param string $archDescAccessRestrict Archival description access restriction
     *
     * @return UniversalFileFormat
     */
    public function setArchDescAccessRestrict($archDescAccessRestrict)
    {
        $this->archDescAccessRestrict = $archDescAccessRestrict;
        return $this;
    }

    /**
     * Get archDescAccessRestrict
     *
     * @return string
     */
    public function getArchDescAccessRestrict()
    {
        return $this->archDescAccessRestrict;
    }

    /**
     * Set archDescLegalStatus
     *
     * @param string $archDescLegalStatus Archival description legal status
     *
     * @return UniversalFileFormat
     */
    public function setArchDescLegalStatus($archDescLegalStatus)
    {
        $this->archDescLegalStatus = $archDescLegalStatus;
        return $this;
    }

    /**
     * Get archDescLegalStatus
     *
     * @return string
     */
    public function getArchDescLegalStatus()
    {
        return $this->archDescLegalStatus;
    }
}
