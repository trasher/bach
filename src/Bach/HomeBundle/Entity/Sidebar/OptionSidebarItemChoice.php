<?php
/**
 * Bach sidebar choice item
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity\Sidebar;

/**
 * Bach sidebar choice item
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class OptionSidebarItemChoice
{
    private $_alias;
    private $_value;
    private $_selected = false;

    /**
     * Item constructor
     *
     * @param string $alias Item alias
     * @param mixed  $value Item value
     */
    public function __construct($alias, $value)
    {
        $this->_alias = $alias;
        $this->_value = $value;
    }

    /**
     * Get item alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * Get item value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Is item selected
     *
     * @return boolean
     */
    public function isSelected()
    {
        return $this->_selected;
    }

    /**
     * Set item selected state
     *
     * @param boolean $selected State
     *
     * @return void
     */
    public function setSelected($selected)
    {
        $this->_selected = $selected;
    }
}
