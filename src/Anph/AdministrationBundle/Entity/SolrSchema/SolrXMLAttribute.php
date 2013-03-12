<?php
namespace Anph\AdministrationBundle\Entity\SolrSchema;

class SolrXMLAttribute
{
	protected $name;
	protected $value;

    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
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
     * Set attribute's value
     *
     * @param string $value
     * @return SolrXMLAttribute
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get attribute's value
     *
     * @return string 
     */
    public function getValue()
    {
        if ($this->name !== 'name' && $this->name !== 'type' && $this->name !== 'default') {
            switch ($this->value) {
                case 'true' :
                    return true;
                case 'false' :
                    return false;
                default :
                    return $this->value;
            }
        }
        return $this->value;
    }
}