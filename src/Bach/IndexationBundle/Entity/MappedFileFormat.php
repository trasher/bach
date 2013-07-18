<?php

namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class MappedFileFormat
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
     * @ORM\Column(type="string", nullable=true, length=250)
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
     * @ORM\Column(type="string", nullable=true, length=15)
     */
    protected $archDescLangOrigination;

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
     * @ORM\ManyToOne(targetEntity="Document")
     * @ORM\JoinColumn(name="doc_id", referencedColumnName="id")
     */
    protected $doc_id;

    /**
      * The constructor
      *
      * @param array $data The input data
      */
    public function __construct($data)
    {
        $this->parseData($data);
    }

    /**
     * Proceed data parsing
     *
     * @param array $data Data to parse
     *
     * @return void
     */
    protected function parseData($data)
    {
        foreach ($data as $key=>$datum) {
            if (property_exists($this, $key)) {
                $this->$key = $datum;
            }
        }
    }

    /**
     * Set headerId
     *
     * @param string $headerId
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
     * @param string $headerAuthor
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
     * @param \DateTime $headerDate
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
     * @param string $headerPublisher
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
     * @param string $headerAddress
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
     * @param string $headerSubtitle
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
     * @param string $headerLanguage
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
     * @param string $archDescUnitId
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
     * @param string $archDescUnitTitle
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
     * @param string $archDescUnitDate
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
     * @param string $archDescDimension
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
     * @param string $archDescRepository
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
     * @param string $archDescLangMaterial
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
     * Set archDescLangOrigination
     *
     * @param string $archDescLangOrigination
     * @return UniversalFileFormat
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
     * @param string $archDescScopeContent
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
     * @param string $archDescAccruals
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
     * @param string $archDescArrangement
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
     * @param string $archDescAccessRestrict
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
     * @param string $archDescLegalStatus
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

    /**
     * Set doc_id
     *
     * @param \Bach\IndexationBundle\Entity\Document $docId
     * @return UniversalFileFormat
     */
    public function setDocId(\Bach\IndexationBundle\Entity\Document $docId = null)
    {
        $this->doc_id = $docId;
    
        return $this;
    }

    /**
     * Get doc_id
     *
     * @return \Bach\IndexationBundle\Entity\Document 
     */
    public function getDocId()
    {
        return $this->doc_id;
    }
}
