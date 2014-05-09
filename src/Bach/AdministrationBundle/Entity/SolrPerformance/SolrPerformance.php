<?php
/**
 * Bach solr performance
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

namespace Bach\AdministrationBundle\Entity\SolrPerformance;

use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use DOMDocument;
use DOMNodeList;

/**
 * Bach solr performance
 * This class contains some functionalities for Solr performance enhancing,
 * more precisely about different types of cache configuration.
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolrPerformance
{
    const ROOT_TAG = 'config';
    const QUERY_TAG = 'query';
    const QUERY_RESULT_WIN_SIZE_TAG = 'queryResultWindowSize';
    const QUERY_RESULT_MAX_DOCS_CACHED_TAG = 'queryResultMaxDocsCached';
    const DOCUMENT_CACHE_TAG = 'documentCache';
    const QUERY_RESULT_CACHE_TAG = 'queryResultCache';
    const FILTER_CACHE_TAG = 'filterCache';

    private $_doc;
    private $_path;

    /**
     * Main constructor
     *
     * @param SolrCoreAdmin $sca      Solr core admin instance
     * @param string        $coreName Core name
     */
    public function __construct(SolrCoreAdmin $sca, $coreName)
    {
        $this->_path = $sca->getConfigPath($coreName);
        $this->_doc = new DOMDocument();
        $this->_doc->formatOutput = true;
        $this->_doc->preserveWhiteSpace = false;
        $this->_doc->load($this->_path);
    }

    /**
     * An optimization for use with the queryResultCache. When a search
     * is requested, a superset of the requested number of document ids
     * are collected.  For example, if a search for a particular query
     * requests matching documents 10 through 19, and queryWindowSize is 50,
     * then documents 0 through 49 will be collected and cached.  Any further
     * requests in that range can be satisfied via the cache. Returns NULL if
     * can not find the appropriate tag;.
     *
     * @return NULL|string
     */
    public function getQueryResultWindowsSize()
    {
        $nodeList = $this->_doc->getElementsByTagName(
            self::QUERY_RESULT_WIN_SIZE_TAG
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * An optimization for use with the queryResultCache. When a search
     * is requested, a superset of the requested number of document ids
     * are collected.  For example, if a search for a particular query
     * requests matching documents 10 through 19, and queryWindowSize is 50,
     * then documents 0 through 49 will be collected and cached.  Any further
     * requests in that range can be satisfied via the cache. Returns NULL if
     * can not find the appropriate tag.
     *
     * @param int $number Number of documents that will be cached
     *
     * @return NULL|\Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setQueryResultWindowsSize($number)
    {
        $this->_createTagInQueryTag(self::QUERY_RESULT_WIN_SIZE_TAG);
        $nodeList = $this->_doc->getElementsByTagName(
            self::QUERY_RESULT_WIN_SIZE_TAG
        );
        return $this->_setNodeValue($nodeList, $number);
    }

    /**
     * Maximum number of documents to cache for any entry in the queryResultCache.
     * Returns NULL if can not find the appropriate tag;.
     *
     * @return NULL|string
     */
    public function getQueryResultMaxDocsCached()
    {
        $nodeList = $this->_doc->getElementsByTagName(
            self::QUERY_RESULT_MAX_DOCS_CACHED_TAG
        );
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }

    /**
     * Maximum number of documents to cache for any entry in the queryResultCache.
     * Returns NULL if can not find the appropriate tag;.
     *
     * @param int $number Number of documents
     *
     * @return NULL|\Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setQueryResultMaxDocsCached($number)
    {
        $this->_createTagInQueryTag(self::QUERY_RESULT_MAX_DOCS_CACHED_TAG);
        $nodeList = $this->_doc->getElementsByTagName(
            self::QUERY_RESULT_MAX_DOCS_CACHED_TAG
        );
        return $this->_setNodeValue($nodeList, $number);
    }

    /**
     * Document Cache caches Lucene Document objects (the stored fields
     * for each document).
     * Since Lucene internal document ids are transient, this cache will
     * not be autowarmed.
     * Returns: an array containing Document Cache Parameters in order :
     * class, size, initialSize;
     *          NULL if can not find the appropriate tag;;
     *
     * @return NULL|array(string)
     */
    public function getDocumentCacheParameters()
    {
        $nodeList = $this->_doc->getElementsByTagName(self::DOCUMENT_CACHE_TAG);
        return $this->_getCacheAttributes($nodeList);
    }

    /**
     * Document Cache caches Lucene Document objects (the stored fields
     * for each document).
     * Since Lucene internal document ids are transient, this cache will
     * not be autowarmed.
     * Returns NULL if can not find the appropriate tag;.
     *
     * @param string $class       The SolrCache implementation LRUCache
     *                            (LRUCache or FastLRUCache)
     * @param string $size        The maximum number of entries in the cache
     * @param string $initialSize The initial capacity (number of entries)
     *                            of the cache
     *
     * @return NULL|\Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setDocumentCacheParameters($class, $size, $initialSize)
    {
        $this->_createTagInQueryTag(self::DOCUMENT_CACHE_TAG);
        $nodeList = $this->_doc->getElementsByTagName(self::DOCUMENT_CACHE_TAG);
        return $this->_setCacheAttributes($nodeList, $class, $size, $initialSize);
    }

    /**
     * Query Result Cache caches results of searches - ordered lists
     * of document ids (DocList)
     * based on a query, a sort, and the range of documents requested.
     * Returns: an array containing Query Result Cache Parameters in order :
     * class, size, initialSize, autowarmCount;
     *          NULL if can not find the appropriate tag;;
     *
     * @return NULL|array(string)
     */
    public function getQueryResultCacheParameters()
    {
        $nodeList = $this->_doc->getElementsByTagName(self::QUERY_RESULT_CACHE_TAG);
        return $this->_getCacheAttributes($nodeList);
    }

    /**
     * Query Result Cache caches results of searches - ordered lists of
     * document ids (DocList)
     * based on a query, a sort, and the range of documents requested. 
     * Returns NULL if can not find the appropriate tag;.
     *
     * @param string $class         The SolrCache implementation LRUCache
     *                              (LRUCache or FastLRUCache)
     * @param string $size          The maximum number of entries in the cache
     * @param string $initialSize   The initial capacity (number of entries)
     *                              of the cache
     * @param string $autowarmCount The number of entries to prepopulate
     *                              from and old cache.
     *
     * @return NULL|\Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setQueryResultCacheParameters(
        $class, $size, $initialSize, $autowarmCount
    ) {
        $this->_createTagInQueryTag(self::QUERY_RESULT_CACHE_TAG);
        $nodeList = $this->_doc->getElementsByTagName(self::QUERY_RESULT_CACHE_TAG);
        return $this->_setCacheAttributes(
            $nodeList,
            $class,
            $size,
            $initialSize,
            $autowarmCount
        );
    }

    /**
     * Cache used by SolrIndexSearcher for filters (DocSets),
     * unordered sets of *all* documents that match a query.
     * When a new searcher is opened, its caches may be prepopulated or
     * "autowarmed" using data from caches in the old searcher. autowarmCount
     * is the number of items to prepopulate.  For LRUCache, the autowarmed
     * items will be the most recently accessed items.
     * Returns: an array containing Filter Cache Parameters in order :
     * class, size, initialSize, autowarmCount;
     *          NULL if can not find the appropriate tag;;
     *
     * @return NULL|array(string)
     */
    public function getFilterCacheParameters()
    {
        $nodeList = $this->_doc->getElementsByTagName(self::FILTER_CACHE_TAG);
        return $this->_getCacheAttributes($nodeList);
    }

    /**
     * Cache used by SolrIndexSearcher for filters (DocSets), unordered sets
     * of *all* documents that match a query.  When a new searcher is opened,
     * its caches may be prepopulated or "autowarmed" using data from caches
     * in the old searcher. autowarmCount is the number of items to prepopulate.
     * For LRUCache, the autowarmed items will be the most recently accessed items.
     * Returns NULL if can not find the appropriate tag;.
     *
     * @param string $class         The SolrCache implementation
     *                              (LRUCache or FastLRUCache)
     * @param string $size          The maximum number of entries in the cache
     * @param string $initialSize   The initial capacity (number of entries)
     *                              of the cache
     * @param string $autowarmCount The number of entries to prepopulate
     *                              from and old cache.
     *
     * @return NULL|\Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setFilterCacheParameters(
        $class, $size, $initialSize, $autowarmCount
    ) {
        $this->_createTagInQueryTag(self::FILTER_CACHE_TAG);
        $nodeList = $this->_doc->getElementsByTagName(self::FILTER_CACHE_TAG);
        return $this->_setCacheAttributes(
            $nodeList,
            $class,
            $size,
            $initialSize,
            $autowarmCount
        );
    }

    /**
     * Save changes to Solr configuration file (solrconfig.xml by default).
     * Returns true in success, false otherwise.
     *
     * @return boolean
     */
    public function save()
    {
        return $this->_doc->save($this->_path) !== false ? true: false;
    }

    /**
     * Set attributes in DOMElement object of cache tags.
     * Returns null if $nodeList does not contain any node.
     *
     * @param DOMNodeList $nodeList      Node list
     * @param string      $class         CLass
     * @param string      $size          Size
     * @param string      $initialSize   Initial size
     * @param string      $autowarmCount Warm count
     *
     * @return SolrPerformance
     */
    private function _setCacheAttributes(
        DOMNodeList $nodeList,
        $class,
        $size,
        $initialSize,
        $autowarmCount = null
    ) {
        if ($nodeList->length == 0) {
            return null;
        } else {
            $elt = $nodeList->item(0);
            $elt->setAttribute('class', $class);
            $elt->setAttribute('size', $size);
            $elt->setAttribute('initialSize', $initialSize);
            if ($autowarmCount != null) {
                $elt->setAttribute('autowarmCount', $autowarmCount);
            }
            return $this;
        }
    }

    /**
     * Get attributes of DOMElement object of cache tags.
     * Returns NULL if $nodeList does not contain any node.
     *
     * @param DOMNodeList $nodeList Node list
     *
     * @return array
     */
    private function _getCacheAttributes(DOMNodeList $nodeList)
    {
        if ($nodeList->length == 0) {
            return null;
        } else {
            $elt = $nodeList->item(0);
            $array[] = $elt->getAttribute('class');
            $array[] = $elt->getAttribute('size');
            $array[] = $elt->getAttribute('initialSize');
            if ($elt->hasAttribute('autowarmCount')) {
                $array[] = $elt->getAttribute('autowarmCount');
            }
            return $array;
        }
    }

    /**
     * Set node value for a first DOMNode object from $nodeList.
     * Returns NULL if $nodeList does not contain any node.
     *
     * @param DOMNodeList $nodeList Node list
     * @param string      $value    Value
     *
     * @return SolrPerformance
     */
    private function _setNodeValue(DOMNodeList $nodeList, $value)
    {
        if ($nodeList->length == 0) {
            return null;
        } else {
            $nodeList->item(0)->nodeValue = $value;
            return $this;
        }
    }

    /**
     * Creates $tagName tag in the "query" tag. If "query" tag does not exist,
     * the function creates it in root tag.
     *
     * @param string $tagName Tag name
     *
     * @return void
     */
    private function _createTagInQueryTag($tagName)
    {
        // If 'query' tag does not exist we create and insert it in the root tag.
        $nodeList = $this->_doc->getElementsByTagName(self::QUERY_TAG);
        if ($nodeList->length == 0) {
            $queryNode = $this->_doc->createElement(self::QUERY_TAG);
            $nodeList = $this->_doc->getElementsByTagName(self::ROOT_TAG);
            $nodeList->item(0)->appendChild($queryNode);
        } else {
            $queryNode = $nodeList->item(0);
        }
        // If $tagName tag does not exist we create and insert item
        // in self::QUERY_TAG tag
        $nodeList = $this->_doc->getElementsByTagName($tagName);
        if ($nodeList->length == 0) {
            $newNode = $this->_doc->createElement($tagName);
            $queryNode->appendChild($newNode);
        }
    }
}
