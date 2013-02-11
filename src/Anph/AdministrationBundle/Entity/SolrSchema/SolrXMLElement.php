<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

use Doctrine\ORM\Mapping as ORM;

class SolrXMLElement
{
	protected $name;
	protected $attributes;
	protected $value;
	protected $elements;
	
    /**
     * Constructor
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
     * @param string $name
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
     * @param string $value
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
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes
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
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute $attributes
     */
    public function removeAttribute(SolrXMLAttribute $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * Get attribute by its name.
     * @param string $name
     * @return Ambigous <multitype:, SolrXMLAttribute>|NULL
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
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $elements
     * @return SolrXMLElement
     */
    public function addElement(SolrXMLElement $element)
    {
        $this->elements[] = $element;
    
        return $this;
    }

    /**
     * Remove elements
     *
     * @param \Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement $elements
     */
    public function removeElement(SolrXMLElement $element)
    {
        $this->elements->removeElement($element);
    }

    /**
     * Get elements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getElements()
    {
        return $this->elements;
    }
    
    /**
     * Get all elements with the name $name included in this element.
     * @param string $name
     * @return array(SolrXMLElement)
     */
    public function getElementsByName($name)
    {
        $elements = array();
        if ($this->name == $name) {
            $elements[] = $this;
        }
        foreach($this->elements as $e) {
            $elmts = $e->getElementsByName($name);
            if (count($elmts) != 0) {
                $elements = array_merge($elements, $elmts);
            }
        }
        return $elements;
    }
}