<?php
/**
 * Bach solr XML Element
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

/**
 * Bach solr XML Element
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
     * Remove attributes
     *
     * @param \Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes
     */
    public function removeAttribute(SolrXMLAttribute $attribute)
    {
        $this->attributes->removeElement($attribute);
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
