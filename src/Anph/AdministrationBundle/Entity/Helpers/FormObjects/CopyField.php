<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

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
}
