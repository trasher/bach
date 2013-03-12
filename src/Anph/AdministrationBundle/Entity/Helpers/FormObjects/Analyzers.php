<?php
namespace Anph\AdministrationBundle\Entity\Helpers\FormObjects;

use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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
}
