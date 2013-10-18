<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class DynamicFields
{
    public $dynamicFields;
    
    public function __construct(XMLProcess $xmlP = null)
    {
        $this->dynamicFields = array();
        if ($xmlP != null) {
            $elements = $xmlP->getElementsByName('fields');
            $elements = $elements[0];
            $elements = $elements->getElementsByName('dynamicField');
            foreach ($elements as $f) {
                $this->dynamicFields[] = new DynamicField($f);
            }
        }
    }
    
    public function save(XMLProcess $xmlP)
    {
        $fieldsArray = array();
        $fieldsElt = $xmlP->getElementsByName('fields');
        $fieldsElt = $fieldsElt[0];
        foreach ($fieldsElt->getElements() as $e) {
            if ($e->getName() == 'field') {
                $fieldsArray[] = $e;
            }
        }
        foreach ($this->dynamicFields as $f) {
            $fieldsArray[] = $f->getSolrXMLElement();
        }
        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
