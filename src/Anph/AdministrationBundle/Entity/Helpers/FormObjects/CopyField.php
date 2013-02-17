<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class CopyField
{
    public $source;
    public $dest;
    public $maxChars;

    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('source');
            $this->source = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('dest');
            $this->dest = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('maxChars');
            $this->maxChars = $attr !== null ? $attr->getValue() : null;
        }
    }
}
