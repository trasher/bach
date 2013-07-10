<?php
/**
 * EAD header processing
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Driver\EAD\Parser\XML;

/**
 * EAD header processing
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class EADHeader
{
    private $_xpath;
    private $_values = array();

    /**
     * Constructor
     *
     * @param DOMXPath $xpath      XPath
     * @param DOMNode  $headerNode XML archdesc node
     * @param array    $fields     Known fields
     */
    public function __construct(\DOMXPath $xpath, \DOMNode $headerNode, $fields)
    {
        $this->_xpath = $xpath;
        $this->_parse($headerNode, $fields);
    }

    /**
     * Getter
     *
     * @param string $name Property name to retrieve
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ( array_key_exists(strtolower($name), $this->_values) ) {
            return $this->_values[strtolower($name)];
        } else {
            return null;
        }
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
     * Parse XML
     *
     * @param DOMNode $headerNode XML archdesc node
     * @param array   $fields     Known fields
     *
     * @return void
     */
    private function _parse(\DOMNode $headerNode, $fields)
    {
        foreach ( $fields as $field ) {
            $nodes = $this->_xpath->query($field, $headerNode);
            $this->_values[$field] = array();
            if ( $nodes->length > 0 ) {
                foreach ( $nodes as $key=>$node ) {
                    $this->_values[$field][] = array(
                        'value'         => $node->nodeValue,
                        'attributes'    => $this->_parseAttributes($node->attributes)
                    );
                }
            }
        }
    }

    /**
     * Parse attributes
     *
     * @param DOMNamedNodeMap $attributes DOM node attributes
     *
     * @return array
     */
    private function _parseAttributes(\DOMNamedNodeMap $attributes)
    {
        $return = array();
        foreach ( $attributes as $key=>$attribute ) {
            $return[$key] = $attribute->value;
        }
        return $return;
    }
}
