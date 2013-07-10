<?php 

namespace Bach\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

class Label
{
	private $noticeLength;
	private $noticeStatus;
	private $documentType;
	private $bibLevel;
	private $hierarchyLevelCode;
	private $markerLength;
	private $subAreaLength;
	private $databaseAddress;
	private $encodingLevel;
	private $catalogFormat;
	private $directoryInfo;
	
	const NOTICE_STATUS_UPDATED = "c";
	const NOTICE_STATUS_DESTROYED = "d";
	const NOTICE_STATUS_NEW = "n";
	const NOTICE_STATUS_CHILD = "o";
	const NOTICE_STATUS_INCOMPLETE = "p";
	
	const NOTICE_TYPE_TEXT_NOTHANDSCRIPT = "a";
	const NOTICE_TYPE_TEXT_HANDSCRIPT = "b";
	const NOTICE_TYPE_MUSIC_NOTHANDSCRIPT = "c";
	const NOTICE_TYPE_MUSIC_HANDSCRIPT = "d";
	const NOTICE_TYPE_MAP_NOTHANDSCRIPT = "e";
	const NOTICE_TYPE_MAP_HANDSCRIPT = "f";
	const NOTICE_TYPE_MEDIA_VIDEO = "g";
	const NOTICE_TYPE_MEDIA_AUDIO_NOTMUSIC = "i";
	const NOTICE_TYPE_MEDIA_AUDIO_MUSIC = "j";	
	const NOTICE_TYPE_GRAPHIC_DOCUMENT_2D = "k";
	const NOTICE_TYPE_ELECTRONIC_RESOURCE = "l";
	const NOTICE_TYPE_MULTIMEDIA_DOCUMENT = "m";
	const NOTICE_TYPE_ARTEFACT = "r";
	
	const BIB_LEVEL_ANALYTIC = "a";
	const BIB_LEVEL_INTEGRATE = "i";
	const BIB_LEVEL_MONOGRAPHY = "m";
	const BIB_LEVEL_PUBGROUP = "s";
	const BIB_LEVEL_COLLECTION = "c";
	
	const HIERARCHY_LEVEL_CODE_UNDEFINED = " ";
	const HIERARCHY_LEVEL_CODE_NONE = "0";
	const HIERARCHY_LEVEL_CODE_FIRST = "1";
	const HIERARCHY_LEVEL_CODE_SECOND = "2";
	
	const ENCODING_LEVEL_FULL= " ";
	const ENCODING_LEVEL_SUB1= "1";
	const ENCODING_LEVEL_SUB2= "2";
	const ENCODING_LEVEL_SUB3= "3";
	
	const CATALOG_FORMAT_FULL = " ";
	const CATALOG_FORMAT_INCOMPLETE = "i";
	const CATALOG_FORMAT_INVALID = "n";
	
	public function __construct($data){
		$this->noticeLength = substr($data,0,4);
		$this->noticeStatus = substr($data,5,1);
		$this->documentType = substr($data,6,1);
		$this->bibLevel = substr($data,7,1);
		$this->hierarchyLevelCode = substr($data,8,1);
		//$this->undefinedCode = substr($data,9,1);
		$this->markerLength = substr($data,10,1);
		$this->subAreaLength = substr($data,11,1);
		$this->databaseAddress = substr($data,12,4);
		$this->encodingLevel = substr($data,17,1);
		$this->catalogFormat = substr($data,18,1);
		//$this->def_suppl = substr($data,19,1);
		$this->directoryInfo = substr($data, 20,3 );		
	}
	
	public function getNoticeStatus() {
		return $this->noticeStatus;
	}
	
	public function getNoticeLength() {
		return $this->noticeLength;
	}
	
	public function getDocumentType() {
		return $this->documentType;
	}
	
	public function getBibLevel() {
		return $this->bibLevel;
	}
	
	public function getHierarchyLevelCode() {
		return $this->hierarchyLevelCode;
	}
		
	public function getMarkerLength() {
		return $this->markerLength;
	}
	
	public function getSubAreaLength() {
		return $this->subAreaLength;
	}
	
	public function getDatabaseAddress() {
		return $this->databaseAddress;
	}
	
	public function getEncodingLevel() {
		return $this->encodingLevel;
	}
	
	public function getCatalogFormat() {
		return $this->catalogFormat;
	}
		
	public function getDirectoryInfo() {
		return $this->directoryInfo;
	}
	
	
}

?>