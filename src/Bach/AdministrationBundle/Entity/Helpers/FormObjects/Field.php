<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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
            $this->indexed = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
            $attr = $fieldElt->getAttribute('stored');
            $this->stored = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
            $attr = $fieldElt->getAttribute('multiValued');
            $this->multiValued = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
            $attr = $fieldElt->getAttribute('default');
            $this->default = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('required');
            $this->required = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
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
    
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('field');
        $attr = new SolrXMLAttribute('name', $this->name);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('type', $this->type);
        $elt->addAttribute($attr);
        if ($this->indexed != null) {
            $attr = new SolrXMLAttribute('indexed', $this->indexed ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        if ($this->stored != null) {
            $attr = new SolrXMLAttribute('stored', $this->stored ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        if ($this->multiValued != null) {
            $attr = new SolrXMLAttribute('multiValued', $this->multiValued ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        if ($this->default != '') {
            $attr = new SolrXMLAttribute('default', $this->default);
            $elt->addAttribute($attr);
        }
        if ($this->required != null) {
            $attr = new SolrXMLAttribute('required', $this->required ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        return $elt;
    }
    
    private function toBoolean($value)
    {
        return $value == 'true' ? true : false;
    }
}
