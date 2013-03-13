<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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
        foreach ($fields as $f) {
            $fieldsArray[] = $this->fields->getSolrXMLElement();
        }
        $fieldsElt = $xmlP->getElementsByName('fields');
        $fieldsElt->
    }
}
