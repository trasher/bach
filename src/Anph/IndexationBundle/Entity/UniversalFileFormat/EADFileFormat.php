<?php

namespace Anph\IndexationBundle\Entity\UniversalFileFormat;
use Anph\IndexationBundle\Entity\UniversalFileFormat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="EADUniversalFileFormat")
 */
class EADFileFormat extends UniversalFileFormat {
	protected $archeDescRootUnitid, $archeDescRootUnittitle, $archeDescRootUnitdate, $archeDescRootPhysdesc, $archeDescRootRepository, $archeDescRootLangmaterial, $archeDescRootOrigination, $archeDescRootAcqinfo, $archeDescRootScopecontent, $archeDescRootAccruals, $archeDescRootArrangement, $archeDescRootAccessrestrict, $archeDescRootLegalstatus, $archeDescRootUserestrict, $archeDescRootOriginalsloc, $archeDescRootRelatedmaterial, $archeDescRootOdd, $archeDescRootProcessinfo, $archeDescRootControlaccess;

	protected $cUnitid, $cUnittitle, $cCopcontent, $cControlacces, $cDaoloc;

	public function getArcheDescRootUnitid() {
		return $this->archeDescRootUnitid;
	}

	public function setArcheDescRootUnitid($archeDescRootUnitid) {
		$this->archeDescRootUnitid = $archeDescRootUnitid;
	}

	public function getArcheDescRootUnittitle() {
		return $this->archeDescRootUnittitle;
	}

	public function setArcheDescRootUnittitle($archeDescRootUnittitle) {
		$this->archeDescRootUnittitle = $archeDescRootUnittitle;
	}

	public function getArcheDescRootUnitdate() {
		return $this->archeDescRootUnitdate;
	}

	public function setArcheDescRootUnitdate($archeDescRootUnitdate) {
		$this->archeDescRootUnitdate = $archeDescRootUnitdate;
	}

	public function getArcheDescRootPhysdesc() {
		return $this->archeDescRootPhysdesc;
	}

	public function setArcheDescRootPhysdesc($archeDescRootPhysdesc) {
		$this->archeDescRootPhysdesc = $archeDescRootPhysdesc;
	}

	public function getArcheDescRootRepository() {
		return $this->archeDescRootRepository;
	}

	public function setArcheDescRootRepository($archeDescRootRepository) {
		$this->archeDescRootRepository = $archeDescRootRepository;
	}

	public function getArcheDescRootLangmaterial() {
		return $this->archeDescRootLangmaterial;
	}

	public function setArcheDescRootLangmaterial($archeDescRootLangmaterial) {
		$this->archeDescRootLangmaterial = $archeDescRootLangmaterial;
	}

	public function getArcheDescRootOrigination() {
		return $this->archeDescRootOrigination;
	}

	public function setArcheDescRootOrigination($archeDescRootOrigination) {
		$this->archeDescRootOrigination = $archeDescRootOrigination;
	}

	public function getArcheDescRootAcqinfo() {
		return $this->archeDescRootAcqinfo;
	}

	public function setArcheDescRootAcqinfo($archeDescRootAcqinfo) {
		$this->archeDescRootAcqinfo = $archeDescRootAcqinfo;
	}

	public function getArcheDescRootScopecontent() {
		return $this->archeDescRootScopecontent;
	}

	public function setArcheDescRootScopecontent($archeDescRootScopecontent) {
		$this->archeDescRootScopecontent = $archeDescRootScopecontent;
	}

	public function getArcheDescRootAccruals() {
		return $this->archeDescRootAccruals;
	}

	public function setArcheDescRootAccruals($archeDescRootAccruals) {
		$this->archeDescRootAccruals = $archeDescRootAccruals;
	}

	public function getArcheDescRootArrangement() {
		return $this->archeDescRootArrangement;
	}

	public function setArcheDescRootArrangement($archeDescRootArrangement) {
		$this->archeDescRootArrangement = $archeDescRootArrangement;
	}

	public function getArcheDescRootAccessrestrict() {
		return $this->archeDescRootAccessrestrict;
	}

	public function setArcheDescRootAccessrestrict(
			$archeDescRootAccessrestrict) {
		$this->archeDescRootAccessrestrict = $archeDescRootAccessrestrict;
	}

	public function getArcheDescRootLegalstatus() {
		return $this->archeDescRootLegalstatus;
	}

	public function setArcheDescRootLegalstatus($archeDescRootLegalstatus) {
		$this->archeDescRootLegalstatus = $archeDescRootLegalstatus;
	}

	public function getArcheDescRootUserestrict() {
		return $this->archeDescRootUserestrict;
	}

	public function setArcheDescRootUserestrict($archeDescRootUserestrict) {
		$this->archeDescRootUserestrict = $archeDescRootUserestrict;
	}

	public function getArcheDescRootOriginalsloc() {
		return $this->archeDescRootOriginalsloc;
	}

	public function setArcheDescRootOriginalsloc($archeDescRootOriginalsloc) {
		$this->archeDescRootOriginalsloc = $archeDescRootOriginalsloc;
	}

	public function getArcheDescRootRelatedmaterial() {
		return $this->archeDescRootRelatedmaterial;
	}

	public function setArcheDescRootRelatedmaterial(
			$archeDescRootRelatedmaterial) {
		$this->archeDescRootRelatedmaterial = $archeDescRootRelatedmaterial;
	}

	public function getArcheDescRootOdd() {
		return $this->archeDescRootOdd;
	}

	public function setArcheDescRootOdd($archeDescRootOdd) {
		$this->archeDescRootOdd = $archeDescRootOdd;
	}

	public function getArcheDescRootProcessinfo() {
		return $this->archeDescRootProcessinfo;
	}

	public function setArcheDescRootProcessinfo($archeDescRootProcessinfo) {
		$this->archeDescRootProcessinfo = $archeDescRootProcessinfo;
	}

	public function getArcheDescRootControlaccess() {
		return $this->archeDescRootControlaccess;
	}

	public function setArcheDescRootControlaccess($archeDescRootControlaccess) {
		$this->archeDescRootControlaccess = $archeDescRootControlaccess;
	}

	public function getCUnitid() {
		return $this->cUnitid;
	}

	public function setCUnitid($cUnitid) {
		$this->cUnitid = $cUnitid;
	}

	public function getCUnittitle() {
		return $this->cUnittitle;
	}

	public function setCUnittitle($cUnittitle) {
		$this->cUnittitle = $cUnittitle;
	}

	public function getCCopcontent() {
		return $this->cCopcontent;
	}

	public function setCCopcontent($cCopcontent) {
		$this->cCopcontent = $cCopcontent;
	}

	public function getCControlacces() {
		return $this->cControlacces;
	}

	public function setCControlacces($cControlacces) {
		$this->cControlacces = $cControlacces;
	}

	public function getCDaoloc() {
		return $this->cDaoloc;
	}

	public function setCDaoloc($cDaoloc) {
		$this->cDaoloc = $cDaoloc;
	}

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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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
     * @return EADFileFormat
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