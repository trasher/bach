<?php
/**
 * Dynamic fields form object
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
 * Dynamic fields form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DynamicFields
{
    public $dynamicFields;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlP XMLProcess instance
     */
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
