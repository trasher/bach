<?php
/**
 * Bach attribute
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
 * Bach attribute
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class BachAttribute
{
    private $_name;
    private $_label;
    private $_type;
    private $_required;
    private $_default;
    private $_values;
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
        $this->_name = $elt->getAttribute('name');
        $this->_label = $this->_retrieveNodeValueByLang(
            $elt,
            'label',
            $lang,
            $defaultLang
        );
        $this->_type = $elt->getAttribute('type');
        $this->_required = $elt->getAttribute('required') == 'true' ? true : false;
        $this->_default = $elt->getAttribute('default');
        $this->_values = array();
        $nodeList = $elt->getElementsByTagName('value');
        foreach ($nodeList as $e) {
            $this->_values[$e->nodeValue] = $e->nodeValue;
        }
        $this->_desc = $this->_retrieveNodeValueByLang(
            $elt,
            'desc',
            $lang,
            $defaultLang
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->_required;
    }

    /**
     * Get default
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * Retrieve a node value for a lang
     *
     * @param DOMElement $elt         Original DOM element
     * @param string     $tagName     Tag name
     * @param string     $lang        Lang
     * @param string     $defaultLang Default lang
     *
     * @return string
     */
    private function _retrieveNodeValueByLang(
        DOMElement $elt, $tagName, $lang, $defaultLang
    ) {
        $nodeList = $elt->getElementsByTagName($tagName);
        foreach ($nodeList as $e) {
            if ($e->getAttribute('lang') == $lang) {
                return $e->nodeValue;
            }
        }
        foreach ($nodeList as $e) {
            if ($e->getAttribute('lang') == $defaultLang) {
                return $e->nodeValue;
            }
        }
    }
}
