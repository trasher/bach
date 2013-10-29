<?php
/**
 * Analysers  form object
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

/**
 * Analysers form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Analyzers
{
    public $analyzers;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlP XMLProcess instance
     */
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

    /**
     * Save
     *
     * @param XMLProcess $xmlP XMLProcess instance
     *
     * @return void
     */
    public function save(XMLProcess $xmlP)
    {
        $fieldsArray = array();
        foreach ($this->analyzers as $f) {
            $fieldTypes = $xmlP->getElementsByName('fieldType');
            foreach ($fieldTypes as $t) {
                if ($t->getAttribute('name')->getValue() == $f->name) {
                    $t->setElements(array($f->getSolrXMLElement()));
                }
            }
        }
        $xmlP->saveXML();
    }
}
