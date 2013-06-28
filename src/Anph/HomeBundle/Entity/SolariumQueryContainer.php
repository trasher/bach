<?php
/**
 * Bach Solarium query container
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Anph\HomeBundle\Entity;

/**
 * Bach Solarium query container
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class SolariumQueryContainer
{
    private $_fields = array();

    /**
     * Set field
     *
     * @param string $name  Field name
     * @param string $value Field value
     *
     * @return void
     */
    public function setField($name, $value)
    {
        $this->_fields[$name] = $value;
    }

    /**
     * Get field
     *
     * @param string $name Field name
     *
     * @return string
     */
    public function getField($name)
    {
        return $this->_fields[$name];
    }

    /**
     * Is field known?
     *
     * @param string $name Field name
     *
     * @return boolean
     */
    public function hasField($name)
    {
        return array_key_exists($name, $this->_fields);
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }
}
