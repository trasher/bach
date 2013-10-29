<?php
/**
 * Field type form object
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

/**
 * Field type form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

class FieldType
{
    public $name;
    public $class;
    public $sortMissingLast = null;
    public $sortMissingFirst = null;
    public $positionIncrementGap = null;
    public $autoGeneratePhraseQueries = null;

    /* Attributes that may be added to the application in the future. */
    /*public $indexed;
    public $stored;
    public $multiValued;
    public $omitNorms;
    public $omitTermFreqAndPositions;
    public $omitPositions;*/

    /**
     * Constructor
     *
     * @param SolrXMLElement $fieldElt Solr field
     */
    public function __construct(SolrXMLElement $fieldElt = null)
    {
        if ($fieldElt != null) {
            $attr = $fieldElt->getAttribute('name');
            $this->name = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('class');
            $this->class = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('sortMissingLast');
            if ( $attr !== null ) {
                $this->sortMissingLast = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('sortMissingFirst');
            if ( $attr !== null ) {
                $this->sortMissingFirst = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('positionIncrementGap');
            if ( $attr !== null ) {
                $this->positionIncrementGap = $attr->getValue();
            }
            $attr = $fieldElt->getAttribute('autoGeneratePhraseQueries');
            if ( $attr !== null ) {
                $this->autoGeneratePhraseQueries = $this->_toBoolean(
                    $attr->getValue()
                );
            }
        }
    }

    /**
     * Add field
     *
     * @param XMLProcess $xmlP XMLProcess instance
     *
     * @return void
     */
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

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('fieldType');
        $attr = new SolrXMLAttribute('name', $this->name);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('class', $this->class);
        $elt->addAttribute($attr);
        if ($this->sortMissingLast != null) {
            $attr = new SolrXMLAttribute(
                'sortMissingLast',
                $this->sortMissingLast ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->sortMissingFirst != null) {
            $attr = new SolrXMLAttribute(
                'sortMissingFirst',
                $this->sortMissingFirst ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        $attr = new SolrXMLAttribute(
            'positionIncrementGap',
            $this->positionIncrementGap
        );
        $elt->addAttribute($attr);
        if ($this->autoGeneratePhraseQueries != null) {
            $attr = new SolrXMLAttribute(
                'autoGeneratePhraseQueries',
                $this->autoGeneratePhraseQueries ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        return $elt;
    }

    /**
     * Converts text to boolean...
     *
     * @param string $value Text value
     *
     * @return boolean
     */
    private function _toBoolean($value)
    {
        return $value == 'true' ? true : false;
    }
}
