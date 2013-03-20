<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;

use DOMXPath;
use DateTime;
use DateInterval;

/**
 * Represantation of status of a Solr core
 */
class SolrCoreStatus
{
    const STATUS_XPATH = '/response/lst[@name="status"]';
    
    private $xpath;
    private $coreName;
    private $coreXpath;

    public function __construct($xpath, $coreName) {
        $this->xpath = $xpath;
        $this->coreName = $coreName;
        $this->coreXpath = SolrCoreStatus::STATUS_XPATH . '/lst[@name="' . $coreName . '"]';
    }
    
    /**
     * @return null | string
     */
    public function isDefaultCore()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/bool[@name="isDefaultCore"]');
        if ($nodeList->length == 0) {
            return null;
        }
        return $nodeList->item(0)->nodeValue === 'true' ? true : false;
    }

    /**
     * @return null | string
     */
    public function getInstanceDir()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/str[@name="instanceDir"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return the null | string
     */
    public function getDataDir()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/str[@name="dataDir"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return the null | string
     */
    public function getConfig()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/str[@name="config"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return the null | string
     */
    public function getSchema()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/str[@name="schema"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | DateTime
     */
    public function getStartTime()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/date[@name="startTime"]');
        return $nodeList->length == 0 ? null : new DateTime($nodeList->item(0)->nodeValue);
    }

    /**
     * @return null | string
     */
    public function getUptime()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/long[@name="uptime"]');
        return $nodeList->length == 0 ? null :  date_create_from_format('s', (string) $nodeList->item(0)->nodeValue);
    }

    /**
     * @return null | string
     */
    public function getNumDocs()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/int[@name="numDocs"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | string
     */
    public function getMaxDoc()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/int[@name="maxDoc"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | string
     */
    public function getVersion()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/long[@name="version"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | string
     */
    public function getSegmentCount()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/int[@name="segmentCount"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | string
     */
    public function getCurrent()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/bool[@name="current"]');
        if ($nodeList->length == 0) {
            return null;
        }
        return $nodeList->item(0)->nodeValue === 'true' ? true : false;
    }

    /**
     * @return null | boolean
     */
    public function hasDeletions()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/bool[@name="hasDeletions"]');
        if ($nodeList->length == 0) {
             return null;
        }
        return $nodeList->item(0)->nodeValue === 'true' ? true : false;
    }

    /**
     * @return null | string
     */
    public function getDirectory()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/str[@name="directory"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | string
     */
    public function getSizeInBytes()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/long[@name="sizeInBytes"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * @return null | string
     */
    public function getSize()
    {
        $nodeList = $this->xpath->query($this->coreXpath . '/lst[@name="index"]/str[@name="size"]');
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

}
