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

namespace Bach\HomeBundle\Entity;

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
    private $_filters = array();

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
     * Set filter
     *
     * @param string $name  Field name
     * @param string $value Field value
     *
     * @return void
     */
    public function setFilter($name, $value)
    {
        $this->_filters[$name] = $value;
    }

    /**
     * Set filters
     *
     * @param array $filters Filters
     *
     * @return void
     */
    public function setFilters($filters)
    {
        $this->_filters = $filters;
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
     * Get filter
     *
     * @param string $name Field name
     *
     * @return string
     */
    public function getFilter($name)
    {
        return $this->_filters[$name];
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

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }

}
