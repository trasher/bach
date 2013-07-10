<?php
namespace Bach\AdministrationBundle\Entity\SolrSchema;

use DOMDocument;
use DOMNodeList;

/**
 * This class allow developers to read bachconfig.xml file, which describes some schema.xml tags, attributes etc.
 *
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
    
    private $doc;
    private $lang;
    private $defaultLang;
    
    /**
     * Constructor. Reads xml config file.
     */
    public function __construct()
    {
        $this->doc = new DOMDocument();
        $this->doc->load(__DIR__.'/../../Resources/config/' . self::CONFIG_FILE_NAME);
        $this->lang = $this->doc->getElementsByTagName('lang')->item(0)->nodeValue;
        $this->defaultLang = $this->doc->getElementsByTagName('defaultLang')->item(0)->nodeValue;
    }
    
    /**
     * Get a tag by its name.
     * @param string $name must be one of the class's constants.
     * @return NULL|\Bach\AdministrationBundle\Entity\SolrSchema\BachTag
     */
    public function getTagByName($name)
    {
        $nodeList = $this->doc->getElementsByTagName($tag);
        if ($nodeList->length == 0) {
            return null;
        } else {
            return new BachTag($nodeList->item(0), $this->lang, $this->defaultLang);
        }
    }
    
    /**
     * Get all attributes of the specified tag.
     * @param string $tag must be one of the class's constants.
     * @return multitype:\Bach\AdministrationBundle\Entity\SolrSchema\BachAttribute
     */
    public function getAttributesByTag($tag)
    {
        $nodeList = $this->doc->getElementsByTagName($tag)->item(0)->getElementsByTagName("attribute");
        return $this->retreiveAttributes($nodeList);
    }
    
    /**
     * Get attribute of the specified tag.
     * @param string $tag must be one of the class's constants.
     * @param string $name attribute's name.
     * @return Ambigous <NULL, \Bach\AdministrationBundle\Entity\SolrSchema\BachAttribute>
     */
    public function getAttributeByTag($tag, $name)
    {
        $nodeList = $this->doc->getElementsByTagName($tag)->item(0)->getElementsByTagName("attribute");
        return $this->retreiveAttribute($nodeList, $name);
    }
    
    /**
     * Get some extra attributes of the specific class.
     * @param string $className class name
     * @return multitype:\Bach\AdministrationBundle\Entity\SolrSchema\BachAttribute |NULL
     */
    public function getFieldTypeExtraAttributes($className)
    {
        $nodeList = $this->doc->getElementsByTagName("fieldTypeExtraAttributes");
        foreach ($nodeList as $e) {
            if ($e.getAttribute("class") == $className) {
                $nodeList = $e->getElementsByTagName("attribute");
                return $this->retreiveAttributes($nodeList);
            }
        }
        return null;
    }
    
    /**
     * Get all classes supporting analyzer.
     * @return multitype:NULL
     */
    public function getAnalyzerSupportingClasses()
    {
        $classes = array();
        $nodeList = $this->doc->getElementsByTagName('analyzer')->item(0)->getElementsByTagName('value');
        foreach ($nodeList as $e) {
            $classes[] = $e->nodeValue;
        }
        return $classes;
    }
    
    /**
     * Retreives all attributes from node list;
     * @param DOMNodeList $nodeList
     * @return multitype:\Bach\AdministrationBundle\Entity\SolrSchema\BachAttribute
     */
    private function retreiveAttributes(DOMNodeList $nodeList)
    {
        $attributes = array();
        foreach ($nodeList as $e) {
            $attributes[] = new BachAttribute($e, $this->lang, $this->defaultLang);
        }
        return $attributes;
    }
    
    /**
     * Retrieves an attribute from $nodeList by its name.
     * @param DOMNodeList $nodeList
     * @param string $name
     * @return \Bach\AdministrationBundle\Entity\SolrSchema\BachAttribute|NULL
     */
    private function retreiveAttribute(DOMNodeList $nodeList, $name)
    {
        foreach ($nodeList as $e) {
            if ($e->getAttribute("name") == $name) {
                return new BachAttribute($e, $this->lang, $this->defaultLang);
            }
        }
        return null;
    }
}
