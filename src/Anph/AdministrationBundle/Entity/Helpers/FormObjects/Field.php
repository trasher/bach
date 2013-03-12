<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class Field
{
    public $name;
    public $type;
    public $indexed;
    public $stored;
    public $multiValued;
    public $default;
    public $required;
    /*
     * These attributes can be added to the application in the future.
     */
    /*
    public $omitNorms;
    public $omitTermFreqAndPositions;
    public $omitPositions;
    public $termVectors;
    public $termPositions;
    public $termOffsets;*/
    
    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('name');
            $this->name = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('type');
            $this->type = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('indexed');
            $this->indexed = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('stored');
            $this->stored = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('multiValued');
            $this->multiValued = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('default');
            $this->default = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('required');
            $this->required = $attr !== null ? $attr->getValue() : null;
            /*
             * These attributes can be added to the application in the future.
             */
            /*$this->omitNorms = $element->getAttribute('omitNorms')->getValue();
            $this->omitTermFreqAndPositions = $element->getAttribute('omitTermFreqAndPositions')->getValue();
            $this->omitPositions = $element->getAttribute('omitPositions')->getValue();
            $this->termVectors = $element->getAttribute('termVectors')->getValue();
            $this->termPositions = $element->getAttribute('termPositions')->getValue();
            $this->termOffsets = $element->getAttribute('termOffsets')->getValue();*/
        }
    }
}
