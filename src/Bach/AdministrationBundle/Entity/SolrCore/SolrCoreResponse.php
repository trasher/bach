<?php
/**
 * Bach solr core response
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

use DOMDocument;
use DOMXPath;

/**
 * Bach solr core response encapsulation
 * Represents Solr response to administration queries
 * (error code, message, trace; core status)
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolrCoreResponse
{
    const STATUS_XPATH = '/response/lst[@name="responseHeader"]/int[@name="status"]';
    const ERROR_CODE_XPATH = '/response/lst[@name="error"]/int[@name="code"]';
    const ERROR_MSG_XPATH = '/response/lst[@name="error"]/str[@name="msg"]';
    const ERROR_TRACE_XPATH = '/response/lst[@name="error"]/str[@name="trace"]';
    const CORE_NAMES = '/response/lst[@name="status"]/lst[@name]/@name';

    private $_status;
    private $_xpath;

    /**
     * Constructor. Create DOMDocument object from Solr XMLResponse string
     *
     * @param string $XMLResponse XML string repsonse from Solr
     */
    public function __construct($XMLResponse)
    {
        $doc = new DOMDocument();
        $doc->loadXML($XMLResponse);
        $this->_xpath = new DOMXPath($doc);
        $nodeList = $this->_xpath->query(SolrCoreResponse::STATUS_XPATH);
        if ($nodeList->length == 0) {
            $this->_status = null;
        } else {
            $this->_status = $nodeList->item(0)->nodeValue;
        }
    }

    /**
     * Get response status (0 if all is ok).
     *
     * @return Ambigous <NULL, string>
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Get data import status
     *
     * @return string
     */
    public function getImportStatus()
    {
        $xpath = '/response/str[@name="status"]';
        $status = $this->_xpath->query($xpath);
        return $status->item(0)->nodeValue;
    }

    /**
     * Get data import messages
     *
     * @return DOMNodeList
     */
    public function getImportMessages()
    {
        $xpath = '/response/lst[@name="statusMessages"]';
        $messages = $this->_xpath->query($xpath);
        return $messages->item(0);
    }

    /**
     * Get error code if exist otherwise returns null.
     *
     * @return Ambigous <NULL, string>
     */
    public function getCode()
    {
        $nodeList = $this->_xpath->query(SolrCoreResponse::ERROR_CODE_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get error message if exist otherwise returns null.
     *
     * @return Ambigous <NULL, string>
     */
    public function getMessage()
    {
        $nodeList = $this->_xpath->query(SolrCoreResponse::ERROR_MSG_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Get error trace if exist otherwise returns null.
     *
     * @return Ambigous <NULL, string>
     */
    public function getTrace()
    {
        $nodeList = $this->_xpath->query(SolrCoreResponse::ERROR_TRACE_XPATH);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * If response status is 0 returns true otherwise returns false.
     *
     * @return Ambigous <NULL, string>
     */
    public function isOk()
    {
        return $this->_status == 0 ? true : false;
    }

    /**
     * Get array of core names.
     *
     * @return array<string>
     */
    public function getCoreNames()
    {
        $nodeList = $this->_xpath->query(SolrCoreResponse::CORE_NAMES);
        foreach ($nodeList as $n) {
            $coreNameArray[] = $n->nodeValue;
        }
        return $coreNameArray;
    }

    /**
     * Get status of a Solr core.
     *
     * @param string $coreName Core name
     *
     * @return SolrCoreStatus
     */
    public function getCoreStatus($coreName)
    {
        return new SolrCoreStatus($this->_xpath, $coreName);
    }
}
