<?php
/**
 * Bach Unimarc parser : Stick
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

/**
 * Bach Unimarc parser : Stick
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Stick
{
    private $_ref;
    private $_area = null;

    /**
     * Constructor
     *
     * @param string $block12   Block
     * @param string $areasData Area data
     */
    public function __construct($block12, $areasData)
    {
        $this->_parse($block12, $areasData);
    }

    /**
     * get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->_ref;
    }

    /**
     * Set Area
     *
     * @param Area $a Area
     *
     * @return void
     */
    public function setArea(Area $a)
    {
        $this->_area = $a;
    }

    /**
     * Get area
     *
     * @return Area
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Parse data
     *
     * @param string $data      Data
     * @param string $areasData Area data
     *
     * @return void
     */
    private function _parse($data, $areasData)
    {
        $this->_ref = substr($data, 0, 3);
        $areaLength = intval(substr($data, 3, 4));
        $areaStart = intval(substr($data, 7, 5));
        $this->_area = new Area(substr($areasData, $areaStart, $areaLength));
    }
}
