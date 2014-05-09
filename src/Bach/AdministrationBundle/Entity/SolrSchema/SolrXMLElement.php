<?php
/**
 * Bach solr XML Element
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
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\SolrSchema;

/**
 * Bach solr XML Element
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolrXMLElement
{
    protected $name;
    protected $attributes;
    protected $value;
    protected $elements;

    /**
     * Instanciate XML Element
     *
     * @param string $name  Attribute name
     * @param string $value Attribute value
     *
     * @return void
     */
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = array();
        $this->elements = array();
    }

    /**
     * Set name
     *
     * @param string $name Element name
     *
     * @return SolrXMLElement
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value Value
     *
     * @return SolrXMLElement
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add attributes
     *
     * @param SolrXMLAttribute $attribute Attributes
     *
     * @return SolrXMLElement
     */
    public function addAttribute(SolrXMLAttribute $attribute)
    {
        $this->attributes[] = $attribute;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attribute by its name.
     *
     * @param string $name Required name
     *
     * @return SolrXMLAttribute orNULL
     */
    public function getAttribute($name)
    {
        foreach ($this->attributes as $a) {
            if ($a->getName() == $name) {
                return $a;
            }
        }
        return null;
    }

    /**
     * Add elements
     *
     * @param SolrXMLElement $element XML Element
     *
     * @return SolrXMLElement
     */
    public function addElement(SolrXMLElement $element)
    {
        $this->elements[] = $element;
        return $this;
    }

    /**
     * Get elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set elements
     *
     * @param array $elements Elements
     *
     * @return void
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }

    /**
     * Get all elements matching $name
     *
     * @param string $name Element name
     *
     * @return SolrXMLElement[]
     */
    public function getElementsByName($name)
    {
        $elements = array();
        if ($this->name == $name) {
            $elements[] = $this;
        }
        foreach ( $this->elements as $e ) {
            $elmts = $e->getElementsByName($name);
            if (count($elmts) != 0) {
                $elements = array_merge($elements, $elmts);
            }
        }
        return $elements;
    }
}
