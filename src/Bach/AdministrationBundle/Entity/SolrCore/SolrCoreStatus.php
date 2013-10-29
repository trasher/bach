<?php
/**
 * Bach solr core status
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\SolrCore;

use DateTime;

/**
 * Bach solr core response
 * Represantation of status of a Solr core
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrCoreStatus
{
    const STATUS_XPATH = '/response/lst[@name="status"]';

    private $_xpath;
    private $_coreName;
    private $_coreXpath;

    /**
     * Constructor
     *
     * @param string $xpath    main xpath
     * @param string $coreName Core name
     */
    public function __construct($xpath, $coreName)
    {
        $this->_xpath = $xpath;
        $this->_coreName = $coreName;
        $this->_coreXpath = SolrCoreStatus::STATUS_XPATH .
            '/lst[@name="' . $coreName . '"]';
    }

    /**
     * Is core the default one?
     *
     * @return null | string
     */
    public function isDefaultCore()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/bool[@name="isDefaultCore"]'
        );
        if ($nodeList->length == 0) {
            return null;
        }
        return $nodeList->item(0)->nodeValue === 'true' ? true : false;
    }

    /**
     * Get instance directory
     *
     * @return null | string
     */
    public function getInstanceDir()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/str[@name="instanceDir"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get data directory
     *
     * @return the null | string
     */
    public function getDataDir()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/str[@name="dataDir"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get configuration directory
     *
     * @return the null | string
     */
    public function getConfig()
    {
        $nodeList = $this->_xpath->query($this->_coreXpath . '/str[@name="config"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Gat schema
     *
     * @return the null | string
     */
    public function getSchema()
    {
        $nodeList = $this->_xpath->query($this->_coreXpath . '/str[@name="schema"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get start time
     *
     * @return null | DateTime
     */
    public function getStartTime()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/date[@name="startTime"]'
        );

        if ( $nodeList->length == 0 ) {
            return null;
        } else {
            return new DateTime($nodeList->item(0)->nodeValue);
        }
    }

    /**
     * Get uptime
     *
     * @return null | string
     */
    public function getUptime()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/long[@name="uptime"]'
        );
        return $nodeList->length == 0 ? null :  $nodeList->item(0)->nodeValue;
    }

    /**
     * Get number of docs
     *
     * @return null | string
     */
    public function getNumDocs()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/int[@name="numDocs"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get max docs
     *
     * @return null | string
     */
    public function getMaxDoc()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/int[@name="maxDoc"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get index version
     *
     * @return null | string
     */
    public function getVersion()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/long[@name="version"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get segment count
     *
     * @return null | string
     */
    public function getSegmentCount()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/int[@name="segmentCount"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Is index current one?
     *
     * @return null | boolean
     */
    public function getCurrent()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/bool[@name="current"]'
        );
        if ($nodeList->length == 0) {
            return null;
        }
        return $nodeList->item(0)->nodeValue === 'true' ? true : false;
    }

    /**
     * Do index has deletions?
     *
     * @return null | boolean
     */
    public function hasDeletions()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/bool[@name="hasDeletions"]'
        );
        if ($nodeList->length == 0) {
             return null;
        }
        return $nodeList->item(0)->nodeValue === 'true' ? true : false;
    }

    /**
     * Get index directory
     *
     * @return null | string
     */
    public function getDirectory()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/str[@name="directory"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get index size (in Bytes)
     *
     * @return null | string
     */
    public function getSizeInBytes()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/long[@name="sizeInBytes"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get index size
     *
     * @return null | string
     */
    public function getSize()
    {
        $nodeList = $this->_xpath->query(
            $this->_coreXpath . '/lst[@name="index"]/str[@name="size"]'
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

}
