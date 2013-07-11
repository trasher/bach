<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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
            $this->sortMissingLast = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
            $attr = $fieldElt->getAttribute('sortMissingFirst');
            $this->sortMissingFirst = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
            $attr = $fieldElt->getAttribute('positionIncrementGap');
            $this->positionIncrementGap = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('autoGeneratePhraseQueries');
            $this->autoGeneratePhraseQueries = $attr !== null ? $this->toBoolean($attr->getValue()) : null;
        }
    }
    
    public function addField(XMLProcess $xmlP)
    {
        $solrXMLElt = new SolrXMLElement('dynamicField');
        $attr = new SolrXMLAttribute('name');
        $attr->setValue($this->name);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('class');
        $attr->setValue($this->class);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('sortMissingLast');
        $attr->setValue($this->sortMissingLast);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('sortMissingFirst');
        $attr->setValue($this->sortMissingFirst);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('positionIncrementGap');
        $attr->setValue($this->positionIncrementGap);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('autoGeneratePhraseQueries');
        $attr->setValue($this->autoGeneratePhraseQueries);
        $solrXMLElt->addAttribute($attr);
        $fields = $xmlP->getElementsByName('types');
        $fields->addElement($solrXMLElt);
    }
    
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('fieldType');
        $attr = new SolrXMLAttribute('name', $this->name);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('class', $this->class);
        $elt->addAttribute($attr);
        if ($this->sortMissingLast != null) {
            $attr = new SolrXMLAttribute('sortMissingLast', $this->sortMissingLast ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        if ($this->sortMissingFirst != null) {
            $attr = new SolrXMLAttribute('sortMissingFirst', $this->sortMissingFirst ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        $attr = new SolrXMLAttribute('positionIncrementGap', $this->positionIncrementGap);
        $elt->addAttribute($attr);
        if ($this->autoGeneratePhraseQueries != null) {
            $attr = new SolrXMLAttribute('autoGeneratePhraseQueries', $this->autoGeneratePhraseQueries ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        return $elt;
    }
    
    private function toBoolean($value)
    {
        return $value == 'true' ? true : false;
    }
}