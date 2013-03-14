<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class CopyFields
{
    public $copyFields;
    
    public function __construct(XMLProcess $xmlP = null)
    {
        $this->copyFields = array();
        if ($xmlP != null) {
            $elements = $xmlP->getElementsByName('copyField');
            foreach ($elements as $f) {
                $this->copyFields[] = new CopyField($f);
            }
        }
    }
    
    public function save(XMLProcess $xmlP)
    {
        $fieldsArray = array();
        $fieldsElt = $xmlP->getRootElement();
        foreach ($fieldsElt->getElements() as $e) {
            if ($e->getName() != 'copyField') {
                $fieldsArray[] = $e;
            }
        }
        foreach ($this->copyFields as $f) {
            $fieldsArray[] = $f->getSolrXMLElement();
        }
        
        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
