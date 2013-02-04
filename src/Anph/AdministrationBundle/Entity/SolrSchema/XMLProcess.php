<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use DOMDocument;
use DOMNode;
use DOMElement;

/**
 * This class depends on SolrCoreAdmin class and use SolrXMLAttribute and SolrXMLElement classes
 * to work with schema.xml file (load, save, retreive information).
 * @author TELECOM Nancy group
 *
 */
class XMLProcess
{
    protected $doc;
    protected $xmlVersion;
    protected $xmlEncoding;
    protected $filePath;
    protected $rootElement;
    
    /**
     * XMLProcess constructor. Retreive path to schema.xml file with the $coreName parameter and
     * load this file.
     * @param string $coreName
     */
    public function __construct($coreName)
    {
        $solrCore = new SolrCoreAdmin();
        $this->filePath = $solrCore->getSchemaPath($coreName);
        $this->rootElement = $this->loadXML();
    }
    
    /**
     * Load schema.xml file.
     * @return \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement
     */
    public function loadXML()
    {
        $this->doc = new DOMDocument();
        $this->doc->load($this->filePath);
        $this->xmlVersion = $this->doc->version;
        $this->xmlEncoding = $this->doc->encoding;
    	return $this->loadXMLHelper($this->doc->documentElement);
    }
    
    /**
     * Save schema.xml file.
     * @return DOMDocument
     */
    public function saveXML()
    {
        $this->doc = new DOMDocument($this->xmlVersion, $this->xmlEncoding);
        $rootNode = $this->saveXMLHelper($this->rootElement);
        $this->doc->appendChild($rootNode);
        $this->doc->save($this->filePath);
        return $this->doc;
    }
    
    /**
     * Get path to schema.xml file.
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
    
    /**
     * Get all elements with the name $name.
     * @param string $name
     * @return array(SolrXMLElement)
     */
    public function getElementsByName($name)
    {
        $elements = array();
        if ($this->rootElement->getName() === $name) {
            $elements[] = $this->rootElement;
            return $elements;
        }
        return $this->rootElement->getElementsByName($name);
    }
    
    /**
     * Recursive algorithm of loading schema.xml file.
     * @param DOMNode $node 
     * @param SolrXMLElement $parent
     * @return \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement
     */
    private function loadXMLHelper(DOMNode $node, SolrXMLElement $parent = null)
    {
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE :
                $newNode = new SolrXMLElement($node->nodeName, $node->nodeValue);
                foreach ($node->attributes as $key => $attr) {
                    $this->loadXMLHelper($attr, $newNode);
                }
                foreach($node->childNodes as $child){
                    $this->loadXMLHelper($child, $newNode);
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
     * @param SolrXMLElement $element
     * @param DOMNode $parent
     * @return DOMElement
     */
    private function saveXMLHelper(SolrXMLElement $element, DOMNode $parent = null)
    {
        $domElement = $this->doc->createElement($element->getName(), $element->getValue());
        foreach ($element->getAttributes() as $a) {
            $domElement->setAttribute($a->getName(), $a->getValue());
        }
        foreach ($element->getElements() as $e) {
            $this->saveXMLHelper($e, $domElement);
        }
        if ($parent != null) {
            $parent->appendChild($domElement);
        } else {
            return $domElement;
        }
    }
}