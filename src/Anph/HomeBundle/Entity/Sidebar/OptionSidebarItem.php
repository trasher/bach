<?php
/**
 * Bach sidebar item
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Anph\HomeBundle\Entity\Sidebar;

/**
 * Bach sidebar item
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class OptionSidebarItem
{
    private $_name;
    private $_choices;
    private $_key;

    /**
     * Item constructor
     *
     * @param string $name    Item name
     * @param string $key     Item key
     * @param mixed  $default Default value
     */
    public function __construct($name, $key, $default)
    {
        $this->_name = $name;
        $this->_key = $key;
        $this->default = $default;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Get default value
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Append choice
     *
     * @param OptionSidebarItemChoice $choice Choice to append
     *
     * @return OptionSidebarItem
     */
    public function appendChoice(OptionSidebarItemChoice $choice)
    {
        $this->_choices[$choice->getValue()] = $choice;
        return $this;
    }

    /**
     * Retrieve choices
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->_choices;
    }
}
