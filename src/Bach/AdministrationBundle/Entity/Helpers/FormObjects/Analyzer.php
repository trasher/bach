<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class Analyzer
{
    public $name;
    public $class;
    
    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('name');
            $this->name = $attr !== null ? $attr->getValue() : null;
            $analyzer = $fieldElt->getElementsByName('analyzer');
            $analyzer = $analyzer[0];
            $attr = $analyzer->getAttribute('class');
            $this->class = $attr !== null ? $attr->getValue() : null;
        }
    }
    
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('analyzer');
        if ($this->class != '<-- Aucun -->') {
            $attr = new SolrXMLAttribute('class', $this->class);
            $elt->addAttribute($attr);
        }
        return $elt;
    }
}
