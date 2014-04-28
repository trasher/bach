<?php
/**
 * Bach Solr schema configuration reader
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

use DOMDocument;
use DOMNodeList;

/**
 * Bach Solr schema configuration reader
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class BachSchemaConfigReader
{
    const CONFIG_FILE_NAME = 'BachSolrSchemaConfig.xml';
    const SCHEMA_TAG = 'schema';
    const FIELD_TAG = 'field';
    const DYNAMIC_FIELD_TAG = 'dynamicField';
    const FIELD_TYPE_TAG = 'fieldType';
    const COPY_FIELD_TAG = 'copyField';
    const UNIQUE_KEY_TAG = 'uniqueKey';
    const ANALYZER_TAG = 'analyzer';

    private $_doc;
    private $_lang;
    private $_defaultLang;

    /**
     * Constructor. Reads xml config file.
     */
    public function __construct()
    {
        $this->_doc = new DOMDocument();
        $this->_doc->load(
            __DIR__ . '/../../Resources/config/' . self::CONFIG_FILE_NAME
        );
        $this->_lang = $this->_doc->getElementsByTagName('lang')
            ->item(0)->nodeValue;
        $this->_defaultLang = $this->_doc->getElementsByTagName('defaultLang')
            ->item(0)->nodeValue;
    }

    /**
     * Get a tag by its name.
     *
     * @param string $name must be one of the class's constants.
     *
     * @return NULL|BachTag
     */
    public function getTagByName($name)
    {
        $nodeList = $this->_doc->getElementsByTagName($name);
        if ($nodeList->length == 0) {
            return null;
        } else {
            return new BachTag(
                $nodeList->item(0),
                $this->_lang,
                $this->_defaultLang
            );
        }
    }

    /**
     * Get all attributes of the specified tag.
     *
     * @param string $tag must be one of the class's constants.
     *
     * @return multitype:BachAttribute
     */
    public function getAttributesByTag($tag)
    {
        $nodeList = $this->_doc->getElementsByTagName($tag)
            ->item(0)->getElementsByTagName('attribute');
        return $this->_retrieveAttributes($nodeList);
    }

    /**
     * Get attribute of the specified tag.
     *
     * @param string $tag  must be one of the class's constants.
     * @param string $name attribute's name.
     *
     * @return BachAttribute
     */
    public function getAttributeByTag($tag, $name)
    {
        $nodeList = $this->_doc->getElementsByTagName($tag)
            ->item(0)->getElementsByTagName("attribute");
        return $this->_retrieveAttribute($nodeList, $name);
    }

    /**
     * Get some extra attributes of the specific class.
     *
     * @param string $className class name
     *
     * @return BachAttribute|NULL
     */
    public function getFieldTypeExtraAttributes($className)
    {
        $nodeList = $this->_doc->getElementsByTagName("fieldTypeExtraAttributes");
        foreach ($nodeList as $e) {
            if ($e.getAttribute("class") == $className) {
                $nodeList = $e->getElementsByTagName("attribute");
                return $this->_retrieveAttributes($nodeList);
            }
        }
        return null;
    }

    /**
     * Get all classes supporting analyzer.
     *
     * @return array
     */
    public function getAnalyzerSupportingClasses()
    {
        $classes = array();
        $nodeList = $this->_doc->getElementsByTagName('analyzer')
            ->item(0)->getElementsByTagName('value');
        foreach ($nodeList as $e) {
            $classes[] = $e->nodeValue;
        }
        return $classes;
    }

    /**
     * Retrieves all attributes from node list;
     *
     * @param DOMNodeList $nodeList Node list
     *
     * @return array
     */
    private function _retrieveAttributes(DOMNodeList $nodeList)
    {
        $attributes = array();
        foreach ($nodeList as $e) {
            $attributes[] = new BachAttribute($e, $this->_lang, $this->_defaultLang);
        }
        return $attributes;
    }

    /**
     * Retrieves an attribute from $nodeList by its name.
     *
     * @param DOMNodeList $nodeList Node list
     * @param string      $name     Node name to retrieve
     *
     * @return BachAttribute|NULL
     */
    private function _retrieveAttribute(DOMNodeList $nodeList, $name)
    {
        foreach ($nodeList as $e) {
            if ($e->getAttribute("name") == $name) {
                return new BachAttribute($e, $this->_lang, $this->_defaultLang);
            }
        }
        return null;
    }
}
