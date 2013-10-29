<?php
/**
 * Copy field form object
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

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;


/**
 * Copy field form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CopyField
{
    public $source;
    public $dest;
    public $maxChars;

    /**
     * Constructor
     *
     * @param SolrXMLElement $fieldElt Solr field
     */
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

    /**
     * Add field
     *
     * @param XMLProcess $xmlP XMLProcess instance
     *
     * @return void
     */
    public function addField(XMLProcess $xmlP)
    {
        $solrXMLElt = new SolrXMLElement('copyField');
        $attr = new SolrXMLAttribute('source');
        $attr->setValue($this->source);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('dest');
        $attr->setValue($this->dest);
        $solrXMLElt->addAttribute($attr);
        $attr = new SolrXMLAttribute('maxChars');
        $attr->setValue($this->maxChars);
        $solrXMLElt->addAttribute($attr);
        $schema = $xmlP->getElementsByName('schema');
        $schema->addElement($solrXMLElt);
    }

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
    public function getSolrXMLElement()
    {
        $elt = new SolrXMLElement('copyField');
        $attr = new SolrXMLAttribute('source', $this->source);
        $elt->addAttribute($attr);
        $attr = new SolrXMLAttribute('dest', $this->dest);
        $elt->addAttribute($attr);
        if ($this->maxChars != null) {
            $attr = new SolrXMLAttribute('maxChars', $this->maxChars);
            $elt->addAttribute($attr);
        }
        return $elt;
    }
}
