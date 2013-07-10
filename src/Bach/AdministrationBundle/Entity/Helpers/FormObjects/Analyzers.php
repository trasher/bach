<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class Analyzers
{
    public $analyzers;

    public function __construct(XMLProcess $xmlP = null)
    {
        $this->analyzers = array();
        if ($xmlP != null) {
            $elements = $xmlP->getElementsByName('fieldType');
            foreach ($elements as $f) {
                if ($f->getAttribute('class')->getValue() == 'solr.TextField') {
                    $anzs = $f->getElementsByName('analyzer');
                    if (count($anzs) != 0) {
                        $this->analyzers[] = new Analyzer($f);
                    }
                }
            }
        }
    }
    
    public function save(XMLProcess $xmlP)
    {
        $fieldsArray = array();
        foreach ($this->analyzers as $f) {
            $fieldTypes = $xmlP->getElementsByName('fieldType');
            foreach ($fieldTypes as $t) {
                if ($t->getAttribute('name')->getValue() == $f->name) {
                    $t->setElements(array($f->getSolrXMLElement()));
                }
            }
        }
        $xmlP->saveXML();
    }
}
