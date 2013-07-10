<?php
/**
 * Bach sidebar option
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

use Symfony\Component\HttpFoundation\Request;

/**
 * Bach sidebar option
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class OptionSidebar
{
    private $_items = array();
    private $_request = null;
    private $_path = null;

    /**
     * Append new option
     *
     * @param OptionSidebarItem $item Item to append
     *
     * @return OptionsSidebar
     */
    public function append(OptionSidebarItem $item)
    {
        $this->_items[] = $item;
        return $this;
    }

    /**
     * Get binded request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Get item value
     *
     * @param string $key Item key
     *
     * @return mixed
     */
    public function getItemValue($key)
    {
        foreach ( $this->_items as $item ) {
            if ( $item->getKey() == $key ) {
                $choices = $item->getChoices();
                foreach ( $choices as $choice ) {
                    if ( $choice->isSelected() ) {
                        return $choice->getValue();
                    }
                }
                return null;
            }
        }

        return null;
    }

    /**
     * Bind request
     *
     * @param Request $request Request to bind to
     * @param string  $path    Path
     *
     * @return void
     */
    public function bind(Request $request, $path)
    {
        $this->_request = $request;
        $this->_path = $path;

        foreach ( $this->_items as $item ) {
            $found = false;
            $choices = $item->getChoices();
            foreach ( $choices as $choice ) {
                $get = $this->_request->query->get(
                    $item->getKey(),
                    $item->getDefault(),
                    false
                );

                if ( $get == $choice->getValue() ) {
                    $found = true;
                    $choice->setSelected(true);
                }
            }

            if ( !$found ) {
                $choices = $item->getChoices();
                $choices[$item->getDefault()]->setSelected(true);
            }
        }
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
}
