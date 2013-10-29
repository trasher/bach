<?php
/**
 * Fields form object
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
 * Fields form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Fields
{
    public $fields;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlP XMLProcess instance
     */
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
        foreach ($this->fields as $f) {
            $fieldsArray[] = $f->getSolrXMLElement();
        }
        $fieldsElt = $xmlP->getElementsByName('fields');
        $fieldsElt = $fieldsElt[0];
        foreach ($fieldsElt->getElements() as $e) {
            if ($e->getName() == 'dynamicField') {
                $fieldsArray[] = $e;
            }
        }
        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
