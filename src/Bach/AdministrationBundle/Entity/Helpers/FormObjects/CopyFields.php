<?php
/**
 * Copy fields form object
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
 * Copy fields form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CopyFields
{
    public $copyFields;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlP XMLProcess instance
     */
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
        $fieldsElt = $xmlP->getRootElement();
        foreach ($fieldsElt->getElements() as $e) {
            if ($e->getName() != 'copyField') {
                $fieldsArray[] = $e;
            }
        }
        foreach ($this->copyFields as $f) {
            $fieldsArray[] = $f->getSolrXMLElement();
        }

        $fieldsElt->setElements($fieldsArray);
        $xmlP->saveXML();
    }
}
