<?php 

namespace Anph\IndexationBundle\Entity\UniversalFileFormat;

use Anph\IndexationBundle\Entity\UniversalFileFormat;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="UNIMARCUniversalFileFormat")
*/
class UNIMARCFileFormat extends UniversalFileFormat
{
	protected $accessRestrict;
    /**
     * @var integer
     */
    private $uniqid;

    /**
     * @var string
     */
    private $headerId;

    /**
     * @var string
     */
    private $headerSubtitle;

    /**
     * @var string
     */
    private $headerAuthor;

    /**
     * @var string
     */
    private $headerDate;

    /**
     * @var string
     */
    private $headerPublisher;

    /**
     * @var string
     */
    private $headerAddress;

    /**
     * @var string
     */
    private $headerLanguage;

    /**
     * @var string
     */
    private $archDescUnitId;

    /**
     * @var string
     */
    private $archDescUnitTitle;

    /**
     * @var string
     */
    private $archDescUnitDate;

    /**
     * @var string
     */
    private $archDescDimension;

    /**
     * @var string
     */
    private $archDescRepository;

    /**
     * @var string
     */
    private $archDescLangMaterial;

    /**
     * @var string
     */
    private $archDescLangOrigination;

    /**
     * @var string
     */
    private $archDescAcqInfo;

    /**
     * @var string
     */
    private $archDescScopeContent;

    /**
     * @var string
     */
    private $archDescAccruals;

    /**
     * @var string
     */
    private $archDescArrangement;

    /**
     * @var string
     */
    private $archDescAccessRestrict;

    /**
     * @var string
     */
    private $archDescLegalStatus;


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
     * Set headerId
     *
     * @param string $headerId
     * @return UNIMARCFileFormat
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
     * Set headerSubtitle
     *
     * @param string $headerSubtitle
     * @return UNIMARCFileFormat
     */
    public function setHeaderSubtitle($headerSubtitle)
    {
        $this->headerSubtitle = $headerSubtitle;
    
        return $this;
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
     * Set headerAuthor
     *
     * @param string $headerAuthor
     * @return UNIMARCFileFormat
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
     * @param string $headerDate
     * @return UNIMARCFileFormat
     */
    public function setHeaderDate($headerDate)
    {
        $this->headerDate = $headerDate;
    
        return $this;
    }

    /**
     * Get headerDate
     *
     * @return string 
     */
    public function getHeaderDate()
    {
        return $this->headerDate;
    }

    /**
     * Set headerPublisher
     *
     * @param string $headerPublisher
     * @return UNIMARCFileFormat
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
     * @param string $headerAddress
     * @return UNIMARCFileFormat
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
     * Set headerLanguage
     *
     * @param string $headerLanguage
     * @return UNIMARCFileFormat
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
     * Set archDescUnitId
     *
     * @param string $archDescUnitId
     * @return UNIMARCFileFormat
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
     * @param string $archDescUnitTitle
     * @return UNIMARCFileFormat
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
     * @param string $archDescUnitDate
     * @return UNIMARCFileFormat
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
     * @param string $archDescDimension
     * @return UNIMARCFileFormat
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
     * @param string $archDescRepository
     * @return UNIMARCFileFormat
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
     * @param string $archDescLangMaterial
     * @return UNIMARCFileFormat
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
     * Set archDescLangOrigination
     *
     * @param string $archDescLangOrigination
     * @return UNIMARCFileFormat
     */
    public function setArchDescLangOrigination($archDescLangOrigination)
    {
        $this->archDescLangOrigination = $archDescLangOrigination;
    
        return $this;
    }

    /**
     * Get archDescLangOrigination
     *
     * @return string 
     */
    public function getArchDescLangOrigination()
    {
        return $this->archDescLangOrigination;
    }

    /**
     * Set archDescAcqInfo
     *
     * @param string $archDescAcqInfo
     * @return UNIMARCFileFormat
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
     * @param string $archDescScopeContent
     * @return UNIMARCFileFormat
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
     * @param string $archDescAccruals
     * @return UNIMARCFileFormat
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
     * @param string $archDescArrangement
     * @return UNIMARCFileFormat
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
     * @param string $archDescAccessRestrict
     * @return UNIMARCFileFormat
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
     * @param string $archDescLegalStatus
     * @return UNIMARCFileFormat
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