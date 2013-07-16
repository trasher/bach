<?php
/**
 * EAD archdesc processing
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
 * EAD archdesc processing
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
class EADArchDesc
{
    private $_xpath;
    private $_values = array();

    /**
     * Constructor
     *
     * @param DOMXPath $xpath        XPath
     * @param DOMNode  $archDescNode XML archdesc node
     * @param array    $fields       Known fields
     */
    public function __construct(\DOMXPath $xpath, \DOMNode $archDescNode, $fields)
    {
        $this->_xpath = $xpath;
        $this->_parse($archDescNode, $fields);
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
     * @param DOMNode $archDescNode XML archdesc node
     * @param array   $fields       Known fields
     *
     * @return void
     */
    private function _parse(\DOMNode $archDescNode, $fields)
    {
        $results = array();
        $results['root'] = array();

        $rootFields = $fields['root'];

        foreach ( $rootFields as $field ) {
            $nodes = $this->_xpath->query($field, $archDescNode);

            if ( $nodes->length > 0 ) {
                $results['root'][$field] = array();
                foreach ( $nodes as $key=>$node ) {
                    $results['root'][$field][] = array(
                        'value'         => $node->nodeValue,
                        'attributes'    => $this->_parseAttributes($node->attributes)
                    );
                }
            }
        }

        // Let's go parsing C node recursively
        $results['c'] = $this->_recursiveCNodeSearch(
            $archDescNode->getElementsByTagName('dsc')->item(0),
            $fields['c']
        );

        $this->_values = $results;
    }

    /**
     * Search c nodes recursively
     *
     * @param DOMNode $rootNode XML root node
     * @param array   $fields   Known fields
     * @param array   $parents  Node parents
     *
     * @return array
     */
    private function _recursiveCNodeSearch(\DOMNode $rootNode,
        $fields, $parents = array()
    ) {
        $results = array();

        $cNodes = $this->_xpath->query('c', $rootNode);

        foreach ( $cNodes as $cNode ) {
            $nodeid = $cNode->getAttribute('id');
            $results[$nodeid] = array('parents' => $parents);

            //keep original fragment, without children
            $frag = clone $cNode;
            $child = $this->_xpath->query('c', $frag);
            if ( count($child) > 0 ) {
                foreach ( $child as $oldc ) {
                    $frag->removeChild($oldc);
                }
            }
            $results[$nodeid]['fragment'] = $frag->ownerDocument->saveXML($frag);

            foreach ( $fields as $field ) {
                //with child inheritance
                //$nodes = $this->_xpath->query($field, $cNode);
                //without child inheritance
                $nodes = $this->_xpath->query($field, $frag);
                $results[$nodeid][$field] = array();

                if ( $nodes->length > 0 ) {
                    foreach ( $nodes as $key=>$node ) {
                        $results[$nodeid][$field][] = array(
                            'value'         => $node->nodeValue,
                            'attributes'    => $this->_parseAttributes(
                                $node->attributes
                            )
                        );
                    }
                }
            }

            if ( $this->_xpath->query('c', $cNode)->length > 0 ) {
                $results = array_merge(
                    $results,
                    $this->_recursiveCNodeSearch(
                        $cNode,
                        $fields,
                        array_merge(
                            $parents,
                            array(
                                $nodeid
                            )
                        )
                    )
                );
            }
        }
        return $results;
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
