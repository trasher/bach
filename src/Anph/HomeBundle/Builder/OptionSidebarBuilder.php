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

namespace Anph\HomeBundle\Builder;

use Anph\HomeBundle\Entity\Sidebar\OptionSidebar;

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
        $urlParams = $this->_sidebar->getRequest()->query->all();

        foreach ( $items as $item ) {
            $output[$item->getName()] = array();
            $tempUrlParams = $urlParams;

            $choices = $item->getChoices();
            foreach ( $choices as $choice ) {
                $tempUrlParams[$item->getKey()] = $choice->getValue();
                $output[$item->getName()][] = array(
                    'alias'     => $choice->getAlias(),
                    'key'       => $item->getKey(),
                    'value'     => $choice->getValue(),
                    'selected'  => $choice->isSelected(),
                    'url'       => $this->_sidebar->getRequest()->getBaseUrl() .
                        '?' . http_build_query($tempUrlParams)
                );
            }
        }

        return $output;
    }
}
