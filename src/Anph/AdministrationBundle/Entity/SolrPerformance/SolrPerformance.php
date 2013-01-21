<?php
namespace Anph\AdministrationBundle\Entity\SolrPerformance;

use JMS\DiExtraBundle\Annotation\AfterSetup;
use DOMDocument;
use DOMNodeList;

/**
 * 
 * This class contains some functionalities for Solr performance enhancing. More precisely
 * about different types of cache configuration.
 *
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
    
    private $doc;
    private $path;
    
    public function __construct($path)
    {
        $this->path = $path;
        $this->doc = new DOMDocument();
        $this->doc->load($path);
    }
    
    /**
     * An optimization for use with the queryResultCache. When a search
     * is requested, a superset of the requested number of document ids
     * are collected.  For example, if a search for a particular query
     * requests matching documents 10 through 19, and queryWindowSize is 50,
     * then documents 0 through 49 will be collected and cached.  Any further
     * requests in that range can be satisfied via the cache. Returns NULL if
     * can not find the appropriate tag;.
     * @return NULL|string
     */
    public function getQueryResultWindowsSize()
    {
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_RESULT_WIN_SIZE_TAG);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    /**
     * An optimization for use with the queryResultCache. When a search
     * is requested, a superset of the requested number of document ids
     * are collected.  For example, if a search for a particular query
     * requests matching documents 10 through 19, and queryWindowSize is 50,
     * then documents 0 through 49 will be collected and cached.  Any further
     * requests in that range can be satisfied via the cache. Returns NULL if
     * can not find the appropriate tag;.
     * @param int $number
     * @return NULL|\Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setQueryResultWindowsSize($number)
    {
        $this->createTagInQueryTag(self::QUERY_RESULT_WIN_SIZE_TAG);
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_RESULT_WIN_SIZE_TAG);
        return $this->setNodeValue($nodeList, $number);
    }
    
    /**
     * Maximum number of documents to cache for any entry in the queryResultCache.
     * Returns NULL if can not find the appropriate tag;.
     * @return NULL|string
     */
    public function getQueryResultMaxDocsCached()
    {
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_RESULT_MAX_DOCS_CACHED_TAG);
        return $nodeList->length == 0 ? null : $nodeList->item(0)->nodeValue;
    }
    
    /**
     * Maximum number of documents to cache for any entry in the queryResultCache.
     * Returns NULL if can not find the appropriate tag;.
     * @param int $number
     * @return NULL|\Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setQueryResultMaxDocsCached($number)
    {
        $this->createTagInQueryTag(self::QUERY_RESULT_MAX_DOCS_CACHED_TAG);
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_RESULT_MAX_DOCS_CACHED_TAG);
        return $this->setNodeValue($nodeList, $number);
    }
    
    /**
     * Document Cache caches Lucene Document objects (the stored fields for each document).
     * Since Lucene internal document ids are transient, this cache will not be autowarmed.
     * Returns: an array containing Document Cache Parameters in order : class, size, initialSize;
     *          NULL if can not find the appropriate tag;;
     * @return NULL|array(string)
     */
    public function getDocumentCacheParameters()
    {
        $nodeList = $this->doc->getElementsByTagName(self::DOCUMENT_CACHE_TAG);
        return $this->getCacheAttributes($nodeList);
    }
    
    /**
     * Document Cache caches Lucene Document objects (the stored fields for each document).
     * Since Lucene internal document ids are transient, this cache will not be autowarmed.
     * Returns NULL if can not find the appropriate tag;.
     * @param string $class the SolrCache implementation LRUCache or (LRUCache or FastLRUCache)
     * @param int | string $size the maximum number of entries in the cache
     * @param int | string $initialSize the initial capacity (number of entries) of the cache
     * @return NULL|\Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setDocumentCacheParameters($class, $size, $initialSize)
    {
        $this->createTagInQueryTag(self::DOCUMENT_CACHE_TAG);
        $nodeList = $this->doc->getElementsByTagName(self::DOCUMENT_CACHE_TAG);
        return $this->setCacheAttributes($nodeList, $class, $size, $initialSize);
    }
    
    /**
     * Query Result Cache caches results of searches - ordered lists of document ids (DocList)
     * based on a query, a sort, and the range of documents requested.
     * Returns: an array containing Query Result Cache Parameters in order : class, size, initialSize, autowarmCount;
     *          NULL if can not find the appropriate tag;;
     * @return NULL|array(string)
     */
    public function getQueryResultCacheParameters()
    {
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_RESULT_CACHE_TAG);
        return $this->getCacheAttributes($nodeList);
    }
    
    /**
     * Query Result Cache caches results of searches - ordered lists of document ids (DocList)
     * based on a query, a sort, and the range of documents requested. Returns NULL if can not
     * find the appropriate tag;.
     * @param string $class the SolrCache implementation LRUCache or (LRUCache or FastLRUCache)
     * @param int | string $size the maximum number of entries in the cache
     * @param int | string $initialSize the initial capacity (number of entries) of the cache
     * @param int | string $autowarmCount the number of entries to prepopulate from and old cache.
     * @return NULL|\Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setQueryResultCacheParameters($class, $size, $initialSize, $autowarmCount)
    {
        $this->createTagInQueryTag(self::QUERY_RESULT_CACHE_TAG);
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_RESULT_CACHE_TAG);
        return $this->setCacheAttributes($nodeList, $class, $size, $initialSize, $autowarmCount);
    }
    
    /**
     * Cache used by SolrIndexSearcher for filters (DocSets), unordered sets of *all* documents
     * that match a query.  When a new searcher is opened, its caches may be prepopulated or
     * "autowarmed" using data from caches in the old searcher. autowarmCount is the number of items
     * to prepopulate.  For LRUCache, the autowarmed items will be the most recently accessed items.
     * Returns: an array containing Filter Cache Parameters in order : class, size, initialSize, autowarmCount;
     *          NULL if can not find the appropriate tag;;
     * @return NULL|array(string)
     */
    public function getFilterCacheParameters()
    {
        $nodeList = $this->doc->getElementsByTagName(self::FILTER_CACHE_TAG);
        return $this->getCacheAttributes($nodeList);
    }
    
    /**
     * Cache used by SolrIndexSearcher for filters (DocSets), unordered sets of *all* documents
     * that match a query.  When a new searcher is opened, its caches may be prepopulated or
     * "autowarmed" using data from caches in the old searcher. autowarmCount is the number of items
     * to prepopulate.  For LRUCache, the autowarmed items will be the most recently accessed items.
     * Returns NULL if can not find the appropriate tag;.
     * @param string $class the SolrCache implementation LRUCache or (LRUCache or FastLRUCache)
     * @param int | string $size the maximum number of entries in the cache
     * @param int | string $initialSize the initial capacity (number of entries) of the cache
     * @param int | string $autowarmCount the number of entries to prepopulate from and old cache.
     * @return NULL|\Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    public function setFilterCacheParameters($class, $size, $initialSize, $autowarmCount)
    {
        $this->createTagInQueryTag(self::FILTER_CACHE_TAG);
        $nodeList = $this->doc->getElementsByTagName(self::FILTER_CACHE_TAG);
        return $this->setCacheAttributes($nodeList, $class, $size, $initialSize, $autowarmCount);
    }
    
    /**
     * Save changes to Solr configuration file (solrconfig.xml by default).
     * Returns true in success, false otherwise.
     * @return boolean
     */
    public function save()
    {
        return $this->doc->save($this->path) !== false ? true: false;
    }
    
    /**
     * Set attributes in DOMElement object of cache tags.
     * Returns null if $nodeList does not contain any node.
     * @param DOMNodeList $domElement
     * @param string $class
     * @param int | string $size
     * @param int | string $initialSize
     * @param int | string $autowarmCount
     */
    private function setCacheAttributes(DOMNodeList $nodeList, $class, $size, $initialSize, $autowarmCount = null)
    {
        if ($nodeList->length == 0) {
            return null;
        } else {
            $elt = $nodeList->item(0);
            $elt->setAttribute('class' ,$class);
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
     * @param DOMNodeList $nodeList
     * @return NULL|unknown
     */
    private function getCacheAttributes(DOMNodeList $nodeList) {
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
     * @param DOMNodeList $nodeList
     * @param int | string $value
     * @return NULL|\Anph\AdministrationBundle\Entity\SolrPerformance\SolrPerformance
     */
    private function setNodeValue(DOMNodeList $nodeList, $value)
    {
        if ($nodeList->length == 0) {
            return null;
        } else {
            $nodeList->item(0)->nodeValue = $value;
            return $this;
        }
    }
    
    /**
     * Creates $tagName tag in the "query" tag. If "query" tag does not exist, the function
     * creates it in root tag.
     * @param string $tagName
     */
    private function createTagInQueryTag($tagName)
    {
        // If 'query' tag does not exist we create and insert it in the root tag.
        $nodeList = $this->doc->getElementsByTagName(self::QUERY_TAG);
        if ($nodeList->length == 0) {
            $queryNode = $this->doc->createElement(self::QUERY_TAG);
            $nodeList = $this->doc->getElementsByTagName(self::ROOT_TAG);
            $nodeList->item(0)->appendChild($queryNode);
        } else {
            $queryNode = $nodeList->item(0);
        }
        // If $tagName tag does not exist we create and insert it in self::QUERY_TAG tag 
        $nodeList = $this->doc->getElementsByTagName($tagName);
        if ($nodeList->length == 0) {
            $newNode = $this->doc->createElement($tagName);
            $queryNode->appendChild($newNode);
        }
    }
}
