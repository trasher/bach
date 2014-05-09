<?php
/**
 * EAD archdesc processing
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
                    $value = strip_tags(
                        str_replace(
                            '<lb/>',
                            ' ',
                            $rootNode->ownerDocument->saveXML($title_xpath->item(0))
                        )
                    );
                    $current_title = $value;
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
                foreach ( $nodes as $node ) {
                    $value = strip_tags(
                        str_replace(
                            '<lb/>',
                            ' ',
                            $node->ownerDocument->saveXML($node)
                        )
                    );
                    $result[$field][] = array(
                        'value'         => $value,
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
