<?php
/**
 * Bach sidebar builder
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Builder;

use Bach\HomeBundle\Entity\Sidebar\OptionSidebar;

/**
 * Bach sidebar builder
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class OptionSidebarBuilder
{
    private $_sidebar;

    /**
     * Builder constructor
     *
     * @param OptionSidebar $sidebar Sidebar
     */
    public function __construct(OptionSidebar $sidebar)
    {
        $this->_sidebar = $sidebar;
    }

    /**
     * Compile sidebar as an array
     *
     * @return array
     */
    public function compileToArray()
    {
        $output = array();
        $items = $this->_sidebar->getItems();
        $linkValues = array();

        foreach ( $items as $item ) {
            $output[$item->getName()] = array();

            $choices = $item->getChoices();
            foreach ( $choices as $choice ) {
                $output[$item->getName()][] = array(
                    'alias'     => $choice->getAlias(),
                    'key'       => $item->getKey(),
                    'value'     => $choice->getValue(),
                    'selected'  => $choice->isSelected(),
                    'url'       => $this->_sidebar->getPath() . '?' . 
                        $item->getKey() . '=' . $choice->getValue()
                );
            }
        }

        return $output;
    }
}
