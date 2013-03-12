<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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
}
