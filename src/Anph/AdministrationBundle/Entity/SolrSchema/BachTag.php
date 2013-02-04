<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

use DOMElement;

class BachTag
{
    private $name;
    private $parent;
    private $desc;
    
    public function __construct(DOMElement $elt, $lang, $defaultLang)
    {
        $this->name = $elt->nodeName;
        $this->parent = $elt->getAttribute('parent');
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
}
