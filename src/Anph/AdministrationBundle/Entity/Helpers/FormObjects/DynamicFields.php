<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

class DynamicFields
{
    public $dynamicFields;
    
    public function __construct(XMLProcess $xmlP = null)
    {
        $this->dinamicFields = array();
        if ($xmlP != null) {
            $elements = $xmlP->getElementsByName('fields');
            $elements = $elements[0];
            $elements = $elements->getElementsByName('dynamicField');
            foreach ($elements as $f) {
                $this->dinamicFields[] = new DynamicField($f);
            }
        }
    }
}
