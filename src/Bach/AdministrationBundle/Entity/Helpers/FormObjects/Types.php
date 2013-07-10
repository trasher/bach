<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class Types
{
    public $types;
    
    public function __construct(XMLProcess $xmlP = null)
    {
        $this->types = array();
        if ($xmlP != null) {
            $elements = $xmlP->getElementsByName('types');
            $elements = $elements[0];
            $elements = $elements->getElementsByName('fieldType');
            foreach ($elements as $f) {
                $this->types[] = new FieldType($f);
            }
        }
    }
    
    public function save(XMLProcess $xmlP)
    {
        $fieldsArray = array();
        foreach ($this->types as $t) {
            $fieldsArray[] = $t->getSolrXMLElement();
        }
        $fieldsElt = $xmlP->getElementsByName('types');
        $fieldsElt = $fieldsElt[0];
        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
