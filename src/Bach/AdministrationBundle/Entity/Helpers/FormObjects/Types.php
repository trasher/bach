<?php
/**
 * Types form object
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
 * Types form object
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Types
{
    public $types;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlP XMLProcess instance
     */
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
        foreach ($this->types as $t) {
            $fieldsArray[] = $t->getSolrXMLElement();
        }
        $fieldsElt = $xmlP->getElementsByName('types');
        $fieldsElt = $fieldsElt[0];
        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
