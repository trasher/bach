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
    private $_cnodes = 'c|c01|c02|c03|c04|c05|c06|c07|c08|c09|c10|c11|c12|dsc';

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

        $results['root'] = $this->_parseNode(
            $archDescNode,
            array_merge($fields['root'], $fields['c'])
        );
        unset($results['root']['frag']);

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

        $cNodes = $this->_xpath->query(
            $this->_cnodes,
            $rootNode
        );

        foreach ( $cNodes as $cNode ) {
            $nodeid = $cNode->getAttribute('id');

            $results[$nodeid] = $this->_parseNode($cNode, $fields, $parents);

            $frag = $results[$nodeid]['frag'];
            unset($results[$nodeid]['frag']);

            if ( $this->_xpath->query($this->_cnodes, $cNode)->length > 0 ) {
                $current_title = '';
                $title_xpath = $this->_xpath->query('./did/unittitle', $frag);
                if ( $title_xpath->length == 1) {
                    $current_title = $title_xpath->item(0)->nodeValue;
                }
                $results = array_merge(
                    $results,
                    $this->_recursiveCNodeSearch(
                        $cNode,
                        $fields,
                        array_merge(
                            $parents,
                            array(
                                $nodeid => $current_title
                            )
                        )
                    )
                );
            }
        }
        return $results;
    }

    /**
     * Parse a node
     *
     * @param DOMNode $cNode   DOM node
     * @param array   $fields  Known fields
     * @param array   $parents Node parents
     *
     * @return array
     */
    private function _parseNode(\DOMNode $cNode, $fields, $parents = null)
    {
        $result = array();

        if ( $parents !== null ) {
            $result['parents'] = $parents;
        }

        //keep original fragment, without children
        $frag = clone $cNode;
        $child = $this->_xpath->query($this->_cnodes, $frag);
        if ( count($child) > 0 ) {
            foreach ( $child as $oldc ) {
                $frag->removeChild($oldc);
            }
        }
        //remove ordering field as well
        $order = $this->_xpath->query('did/unitid[@type="ordre_c"]', $frag);
        if ( $order->length > 0 ) {
            $result['order'] = $order->item(0)->nodeValue;
            $did = $frag->getElementsByTagName('did')->item(0);
            foreach ( $order as $oldorder ) {
                $did->removeChild($oldorder);
            }
        }
        $result['fragment'] = $frag->ownerDocument->saveXML($frag);
        $result['frag'] = $frag;

        foreach ( $fields as $field ) {
            $nodes = $this->_xpath->query($field, $frag);

            if ( $nodes->length > 0 ) {
                $result[$field] = array();
                foreach ( $nodes as $key=>$node ) {
                    $result[$field][] = array(
                        'value'         => $node->nodeValue,
                        'attributes'    => $this->_parseAttributes(
                            $node->attributes
                        )
                    );
                }
            }
        }
        return $result;
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
