<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

use DOMElement;

class BachAttribute
{
    private $name;
    private $type;
    private $required;
    private $default;
    private $values;
    private $desc;
    
    public function __construct(DOMElement $elt, $lang, $defaultLang)
    {
        $this->name = $elt->getAttribute('name');
        $this->type = $elt->getAttribute('type');
        $this->required = $elt->getAttribute('required');
        $this->default = $elt->getAttribute('default');
        $this->values = array();
        $nodeList = $elt->getElementsByTagName('value');
        foreach ($nodeList as $e) {
            $values[] = $e->nodeValue;
        }
        $this->desc = '';
        $descFound = false;
        $nodeList = $elt->getElementsByTagName('desc');
        foreach ($nodeList as $e) {
            if ($e->getAttribute('lang') == $lang) {
                $this->desc = $e->nodeValue;
                $descFound = true;
                break;
            }
        }
        if (!descFound) {
            foreach ($nodeList as $e) {
                if ($e->getAttribute('lang') == $defaultLang) {
                    $this->desc = $e->nodeValue;
                    $descFound = true;
                    break;
                }
            }
        }
    }
    
    public function getName()
    {
        return $this->name;
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
}
