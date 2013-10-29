<?php
/**
 * Bach Unimarc parser : Notice
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
 * Bach Unimarc parser : notice
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Notice
{
    private $_label;
    private $_sticks;

    /**
     * Constructor
     *
     * @param string $block Data block
     */
    public function __construct($block)
    {
        $labelLength = 24; //longueur du label
        $stickLength = 12; //longueur d'un premier bloc d'étiquette

        $this->_label = new Label(substr($block, 0, $labelLength));

        $stickEnd = strpos($block, chr(30));
        $size  = strlen($block);

        $sticksData = substr($block, $labelLength, $stickEnd-$labelLength);
        $areasData = substr($block, $stickEnd, $size - $stickEnd);

        //split each $stickLength
        $sticksData = str_split($sticksData, $stickLength);
        $sticks = array();

        foreach ( $sticksData as $stickData ) {
            $sticks[] = new Stick($stickData, $areasData);
        }

        $this->_sticks = $sticks;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Get sticks
     *
     * @return array
     */
    public function getSticks()
    {
        return $this->_sticks;
    }
}
