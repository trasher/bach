<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

use DOMElement;

class BachAttribute
{
    private $name;
    private $label;
    private $type;
    private $required;
    private $default;
    private $values;
    private $desc;
    
    public function __construct(DOMElement $elt, $lang, $defaultLang)
    {
        $this->name = $elt->getAttribute('name');
        $this->label = $this->retreiveNodeValueByLang($elt, 'label', $lang, $defaultLang);
        $this->type = $elt->getAttribute('type');
        $this->required = $elt->getAttribute('required') == 'true' ? true : false;
        $this->default = $elt->getAttribute('default');
        $this->values = array();
        $nodeList = $elt->getElementsByTagName('value');
        foreach ($nodeList as $e) {
            $this->values[$e->nodeValue] = $e->nodeValue;
        }
        $this->desc = $this->retreiveNodeValueByLang($elt, 'desc', $lang, $defaultLang);
        
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getLabel()
    {
        return $this->label;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function isRequired()
    {
        return $this->required;
    }
    
    public function getDefault()
    {
        return $this->Default;
    }
    
    public function getValues()
    {
        return $this->values;
    }
    
    public function getDesc()
    {
        return $this->desc;
    }
    
    private function retreiveNodeValueByLang(DOMElement $elt, $tagName, $lang, $defaultLang)
    {
        $nodeList = $elt->getElementsByTagName($tagName);
        foreach ($nodeList as $e) {
            if ($e->getAttribute('lang') == $lang) {
                return $e->nodeValue;
            }
        }
        foreach ($nodeList as $e) {
            if ($e->getAttribute('lang') == $defaultLang) {
                return $e->nodeValue;
            }
        }
    }
}
