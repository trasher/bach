<?php
namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class Fields
{
    public $fields;
    
    public function __construct(XMLProcess $xmlP = null)
    {
        $this->fields = array();
        if ($xmlP != null) {
            $elements = $xmlP->getElementsByName('fields');
            $elements = $elements[0];
            $elements = $elements->getElementsByName('field');
            foreach ($elements as $f) {
                $this->fields[] = new Field($f);
            }
        }
    }
    
    public function save(XMLProcess $xmlP)
    {
        $fieldsArray = array();
        foreach ($this->fields as $f) {
            $fieldsArray[] = $f->getSolrXMLElement();
        }
        $fieldsElt = $xmlP->getElementsByName('fields');
        $fieldsElt = $fieldsElt[0];
        foreach ($fieldsElt->getElements() as $e) {
            if ($e->getName() == 'dynamicField') {
                $fieldsArray[] = $e;
            }
        }
        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
