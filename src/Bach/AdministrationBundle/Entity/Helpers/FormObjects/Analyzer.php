<?php
/**
 * Analyser form object
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
 * Analyser form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Analyzer
{
    public $name;
    public $class;

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
            $analyzer = $fieldElt->getElementsByName('analyzer');
            $analyzer = $analyzer[0];
            $attr = $analyzer->getAttribute('class');
            $this->class = $attr !== null ? $attr->getValue() : null;
        }
    }

    /**
     * Get Solr XML element, with relevant attributes
     *
     * @return SolrXMLElement
     */
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
