<?php
namespace Anph\AdministrationBundle\Entity\SolrCore;

use DOMDocument;
use DOMXPath;

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
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getCode()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::ERROR_CODE_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    public function getMessage()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::ERROR_MSG_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    public function getTrace()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::ERROR_TRACE_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    public function isOk()
    {
        return $this->status == 0 ? true : false;
    }
    
    public function getCoreNames()
    {
        $nodeList = $this->xpath->query(SolrCoreResponse::CORE_NAMES);
        foreach($nodeList as $n) {
            $coreNameArray[] = $n->nodeValue;
        }
        return $coreNameArray;
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