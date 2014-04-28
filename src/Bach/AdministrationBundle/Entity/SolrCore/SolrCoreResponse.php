<?php
/**
 * Bach solr core response
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
