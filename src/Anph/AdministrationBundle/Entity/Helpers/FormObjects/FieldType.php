<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class FieldType
{
    public $name;
    public $class;
    public $sortMissingLast;
    public $sortMissingFirst;
    public $positionIncrementGap;
    public $autoGeneratePhraseQueries;
    
    /*
     * These attributes can be added to the application in the future.
    */
    /*
    public $indexed;
    public $stored;
    public $multiValued;
    public $omitNorms;
    public $omitTermFreqAndPositions;
    public $omitPositions;*/
    
    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('name');
            $this->name = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('class');
            $this->class = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('sortMissingLast');
            $this->sortMissingLast = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('sortMissingFirst');
            $this->sortMissingFirst = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('positionIncrementGap');
            $this->positionIncrementGap = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('autoGeneratePhraseQueries');
            $this->autoGeneratePhraseQueries = $attr !== null ? $attr->getValue() : null;
        }
    }
}
