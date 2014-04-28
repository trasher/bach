<?php
/**
 * Bach schema.xml processor
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

namespace Bach\AdministrationBundle\Entity\SolrSchema;

use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use DOMDocument;
use DOMNode;
use DOMElement;

/**
 * Work with schema.xml file (load, save, retreive information).
 *
 * @category Administration
 * @package  Bach
 * @author   TELECOM Nancy group <none@none.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class XMLProcess
{
    private $_sca;

    protected $doc;
    protected $xmlVersion;
    protected $xmlEncoding;
    protected $filePath;
    protected $rootElement;

    /**
     * XMLProcess constructor. Retreive path to schema.xml file for current core and
     * load this file.
     *
     * @param SolrCoreAdmin $sca      Core admin instance
     * @param string        $coreName Core name
     */
    public function __construct($sca, $coreName)
    {
        $this->_sca = $sca;
        $this->filePath = $this->_sca->getSchemaPath($coreName);
        $this->rootElement = $this->loadXML();
    }

    /**
     * Load schema.xml file.
     *
     * @return SolrXMLElement
     */
    public function loadXML()
    {
        $this->doc = new DOMDocument();
        $this->doc->load($this->filePath);
        $this->xmlVersion = $this->doc->version;
        $this->xmlEncoding = $this->doc->encoding;
        return $this->_loadXMLHelper($this->doc->documentElement);
    }

    /**
     * Save schema.xml file.
     *
     * @return DOMDocument
     */
    public function saveXML()
    {
        $this->doc = new DOMDocument($this->xmlVersion, $this->xmlEncoding);
        $this->doc->formatOutput = true;
        $this->doc->preserveWhiteSpace = false;
        $rootNode = $this->_saveXMLHelper($this->rootElement);
        $this->doc->appendChild($rootNode);
        $this->doc->save($this->filePath);
        return $this->doc;
    }

    /**
     * Get path to schema.xml file.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Get all elements with the name $name.
     *
     * @param string $name Element name
     *
     * @return array(SolrXMLElement)
     */
    public function getElementsByName($name)
    {
        return $this->rootElement->getElementsByName($name);
    }

    /**
     * Get root element
     *
     * @return DOMNode
     */
    public function getRootElement()
    {
        return $this->rootElement;
    }

    /**
     * Recursive algorithm of loading schema.xml file.
     *
     * @param DOMNode        $node   Root node
     * @param SolrXMLElement $parent Parent node
     *
     * @return SolrXMLElement
     */
    private function _loadXMLHelper(DOMNode $node, SolrXMLElement $parent = null)
    {
        switch ($node->nodeType) {
        case XML_ELEMENT_NODE :
            $newNode = new SolrXMLElement($node->nodeName, $node->nodeValue);
            foreach ($node->attributes as $attr) {
                $this->_loadXMLHelper($attr, $newNode);
            }
            foreach ( $node->childNodes as $child ) {
                $this->_loadXMLHelper($child, $newNode);
            }
            if ($parent != null) {
                $parent->addElement($newNode);
            } else {
                return $newNode;
            }
            break;
        case XML_ATTRIBUTE_NODE :
            $newAttribute= new SolrXMLAttribute($node->name, $node->value);
            $parent->addAttribute($newAttribute);
            break;
        case XML_TEXT_NODE :
            $parent->setValue($node->wholeText);
            break;
        }
    }

    /**
     * Recursive algorithm of saving schema.xml file.
     *
     * @param SolrXMLElement $element Element
     * @param DOMNode        $parent  Parent node
     *
     * @return DOMElement
     */
    private function _saveXMLHelper(SolrXMLElement $element, DOMNode $parent = null)
    {
        $domElement = $this->doc->createElement(
            $element->getName(),
            $element->getValue()
        );
        foreach ($element->getAttributes() as $a) {
            $domElement->setAttribute($a->getName(), $a->getValue());
        }
        foreach ($element->getElements() as $e) {
            $this->_saveXMLHelper($e, $domElement);
        }
        if ($parent != null) {
            $parent->appendChild($domElement);
        } else {
            return $domElement;
        }
    }
}
