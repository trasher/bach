<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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
}
