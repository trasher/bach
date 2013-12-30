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

use Bach\HomeBundle\Entity\Filters;

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
    private $_filters;
    private $_order = ViewParams::ORDER_RELEVANCE;

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
     * Set filters
     *
     * @param Filters $filters Filters
     *
     * @return void
     */
    public function setFilters(Filters $filters)
    {
        $this->_filters = $filters;
    }

    /**
     * Set order
     *
     * @param int $order Order
     *
     * @return void
     */
    public function setOrder($order)
    {
        $this->_order = $order;
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

    /**
     * Get order
     *
     * @return int
     */
    public function getOrderField()
    {
        switch ( $this->_order ) {
        case ViewParams::ORDER_TITLE:
            return 'ocUnittitle';
            break;
        case ViewParams::ORDER_DOC_LOGIC:
            return array('archDescUnitTitle', 'elt_order');
            break;
        }
        return $this->_order;
    }

    /**
     * Get order direction
     *
     * @return string
     */
    public function getOrderDirection()
    {
        return ViewParams::ORDER_ASC;
    }

    /**
     * Should query be ordered?
     *
     * @return boolean
     */
    public function isOrdered()
    {
        if ( $this->_order !== ViewParams::ORDER_RELEVANCE ) {
            return true;
        } else {
            return false;
        }
    }
}
