<?php
/**
 * Field form object
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
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;


/**
 * Field form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Field
{
    public $name;
    public $type;
    public $indexed = null;
    public $stored = null;
    public $multiValued = null;
    public $default = null;
    public $required = null;

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
            $attr = $fieldElt->getAttribute('type');
            $this->type = $attr !== null ? $attr->getValue() : null;
            $attr = $fieldElt->getAttribute('indexed');
            if ( $attr !== null ) {
                $this->indexed = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('stored');
            if ( $attr !== null ) {
                $this->stored = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('multiValued');
            if ( $attr !== null ) {
                $this->multiValued = $this->_toBoolean($attr->getValue());
            }
            $attr = $fieldElt->getAttribute('default');
            if ( $attr !== null ) {
                $this->default = $attr->getValue();
            }
            $attr = $fieldElt->getAttribute('required');
            if ( $attr !== null ) {
                $this->required = $this->_toBoolean($attr->getValue());
            }
        }
    }

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('field');
        $attr = new SolrXMLAttribute('name', $this->name);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('type', $this->type);
        $elt->addAttribute($attr);
        if ($this->indexed != null) {
            $attr = new SolrXMLAttribute(
                'indexed',
                $this->indexed ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->stored != null) {
            $attr = new SolrXMLAttribute('stored', $this->stored ? 'true' : 'false');
            $elt->addAttribute($attr);
        }
        if ($this->multiValued != null) {
            $attr = new SolrXMLAttribute(
                'multiValued',
                $this->multiValued ? 'true' : 'false'
            );
            $elt->addAttribute($attr);
        }
        if ($this->default != '') {
            $attr = new SolrXMLAttribute('default', $this->default);
            $elt->addAttribute($attr);
        }
        if ($this->required != null) {
            $attr = new SolrXMLAttribute(
                'required',
                $this->required ? 'true' : 'false'
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
