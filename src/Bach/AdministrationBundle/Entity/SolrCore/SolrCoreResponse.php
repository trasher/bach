<?php
namespace Bach\AdministrationBundle\Entity\SolrCore;

use DOMDocument;
use DOMXPath;

/**
 * Represents Solr response to administration queries (error code, message, trace; core status)
 */
class SolrCoreResponse
{
    const STATUS_XPATH = '/response/lst[@name="responseHeader"]/int[@name="status"]';
    const ERROR_CODE_XPATH = '/response/lst[@name="error"]/int[@name="code"]';
    const ERROR_MSG_XPATH = '/response/lst[@name="error"]/str[@name="msg"]';
    const ERROR_TRACE_XPATH = '/response/lst[@name="error"]/str[@name="trace"]';
    const CORE_NAMES = '/response/lst[@name="status"]/lst[@name]/@name';
    
    private $status;
    private $doc;
    private $xpath;

    /**
     * Constructor. Create DOMDocument object from Solr XMLResponse string
     * @param string $XMLResponse
     */
    public function __construct($XMLResponse)
    {
        $doc = new DOMDocument();
        $doc->loadXML($XMLResponse);
        $this->xpath = new DOMXPath($doc);
        $nodeList = $this->xpath->query(SolrCoreResponse::STATUS_XPATH);
        if ($nodeList->length == 0) {
            $this->status = null;
        } else {
            $this->status = $nodeList->item(0)->nodeValue;
        }
    }
    
    /**
     * Get response status (0 if all is ok).
     * @return Ambigous <NULL, string>
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Get error code if exist otherwise returns null.
     * @return Ambigous <NULL, string>
     */
    public function getCode()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::ERROR_CODE_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    /**
     * Get error message if exist otherwise returns null.
     * @return Ambigous <NULL, string>
     */
    public function getMessage()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::ERROR_MSG_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    /**
     * Get error trace if exist otherwise returns null.
     * @return Ambigous <NULL, string>
     */
    public function getTrace()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::ERROR_TRACE_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    /**
     * If response status is 0 returns true otherwise returns false.
     * @return Ambigous <NULL, string>
     */
    public function isOk()
    {
        return $this->status == 0 ? true : false;
    }
    
    /**
     * Get array of core names.
     * @return array<string>
     */
    public function getCoreNames()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::CORE_NAMES);
        foreach($nodeList as $n) {
            $coreNameArray[] = $n->nodeValue;
        }
        return $coreNameArray;
    }
    
    /**
     * Get status of a Solr core.
     * @return SolrCoreStatus
     */
    public function getCoreStatus($coreName) {
        return new SolrCoreStatus($this->xpath, $coreName);
    }
    
    private function getNodeValue($xpath)
    {
        $nodeList = $this->xpath->query($xpath);
        if ($nodeList->length == 0) {
            return null;
        } else {
           return $nodeList->item(0)->nodeValue;
        }
    }
}