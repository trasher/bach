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
}
