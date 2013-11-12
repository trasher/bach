<?php
/**
 * Bach tag
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\SolrSchema;

use DOMElement;


/**
 * Bach tag
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachTag
{
    private $_name;
    private $_parent;
    private $_desc;

    /**
     * Constructor
     *
     * @param DOMElement $elt         Original DOM element
     * @param string     $lang        Lang
     * @param string     $defaultLang Default lang
     */
    public function __construct(DOMElement $elt, $lang, $defaultLang)
    {
        $this->_name = $elt->nodeName;
        $this->_parent = $elt->getAttribute('parent');
        $this->_desc = '';
        $descFound = false;
        $nodeList = $elt->getElementsByTagName('desc');
        foreach ($nodeList as $e) {
            if ($e->getAttribute('lang') == $lang) {
                $this->_desc = $e->nodeValue;
                $descFound = true;
                break;
            }
        }
        if (!$descFound) {
            foreach ($nodeList as $e) {
                if ($e->getAttribute('lang') == $defaultLang) {
                    $this->_desc = $e->nodeValue;
                    $descFound = true;
                    break;
                }
            }
        }
    }
}
