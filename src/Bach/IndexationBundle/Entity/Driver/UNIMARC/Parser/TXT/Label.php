<?php
/**
 * Bach Unimarc parser : label
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

/**
 * Bach Unimarc parser : label
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Label
{
    private $_noticeLength;
    private $_noticeStatus;
    private $_documentType;
    private $_bibLevel;
    private $_hierarchyLevelCode;
    private $_markerLength;
    private $_subAreaLength;
    private $_databaseAddress;
    private $_encodingLevel;
    private $_catalogFormat;
    private $_directoryInfo;

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

    /**
     * Constructor
     *
     * @param array $data Data
     */
    public function __construct($data)
    {
        $this->_noticeLength = substr($data, 0, 4);
        $this->_noticeStatus = substr($data, 5, 1);
        $this->_documentType = substr($data, 6, 1);
        $this->_bibLevel = substr($data, 7, 1);
        $this->_hierarchyLevelCode = substr($data, 8, 1);
        //$this->_undefinedCode = substr($data, 9, 1);
        $this->_markerLength = substr($data, 10, 1);
        $this->_subAreaLength = substr($data, 11, 1);
        $this->_databaseAddress = substr($data, 12, 4);
        $this->_encodingLevel = substr($data, 17, 1);
        $this->_catalogFormat = substr($data, 18, 1);
        //$this->_def_suppl = substr($data, 19, 1);
        $this->_directoryInfo = substr($data, 20, 3);
    }

    /**
     * Get notice status
     *
     * @return string
     */
    public function getNoticeStatus()
    {
        return $this->_noticeStatus;
    }

    /**
     * Get notice length
     *
     * @return string
     */
    public function getNoticeLength()
    {
        return $this->_noticeLength;
    }

    /**
     * Get document type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->_documentType;
    }

    /**
     * Get bib level
     *
     * @return string
     */
    public function getBibLevel()
    {
        return $this->_bibLevel;
    }

    /**
     * Get herarchy level code
     *
     * @return string
     */
    public function getHierarchyLevelCode()
    {
        return $this->_hierarchyLevelCode;
    }

    /**
     * Get marker length
     *
     * @return string
     */
    public function getMarkerLength()
    {
        return $this->_markerLength;
    }

    /**
     * Get sub area length
     *
     * @return string
     */
    public function getSubAreaLength()
    {
        return $this->_subAreaLength;
    }

    /**
     * Get database address
     *
     * @return string
     */
    public function getDatabaseAddress()
    {
        return $this->_databaseAddress;
    }

    /**
     * Get encoding level
     *
     * @return string
     */
    public function getEncodingLevel()
    {
        return $this->_encodingLevel;
    }

    /**
     * Get catalog format
     *
     * @return string
     */
    public function getCatalogFormat()
    {
        return $this->_catalogFormat;
    }

    /**
     * Get directory informations
     *
     * @return string
     */
    public function getDirectoryInfo()
    {
        return $this->_directoryInfo;
    }

}

