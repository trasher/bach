<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class UniqueKey
{
    public $uniqueKey;
    
    public function __construct(XMLProcess $xmlP = null)
    {
        if ($xmlP != null) {
            $element = $xmlP->getElementsByName('uniqueKey');
            $element = $element[0];
            $this->uniqueKey = $element->getValue();
        }
    }
    
    public function save(XMLProcess $xmlP)
    {
        $elt = $xmlP->getElementsByName('uniqueKey');
        $elt = $elt[0];
        $elt->setValue($this->uniqueKey);
        $xmlP->saveXML();
    }
}
