<?php
/**
 * Bach Unimarc parser : area
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
 * Bach Unimarc parser : area
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

class Area
{
    private $_content;
    private $_ref = null;
    private $_subAreas = array();

    /**
     * Constructor
     *
     * @param string $block Block content
     * @param string $ref   Ref
     */
    public function __construct($block, $ref = null)
    {
        $this->_ref = $ref;
        $this->_parse(trim($block));
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->_ref;
    }

    /**
     * Get sub areas
     *
     * @return array
     */
    public function getSubAreas()
    {
        return $this->_subAreas;
    }

    /**
     * Parse contents
     *
     * @param string $data Data to parse
     *
     * @return void
     */
    private function _parse($data)
    {
        if ( ($pos = strpos($data, chr(31))) !==false ) {
            $subAreas = explode(chr(31), substr($data, $pos));
            foreach ($subAreas as $subArea) {
                $length = mb_strlen($subArea);
                if ($length > 0) {
                    $this->_subAreas[] = new Area(
                        mb_substr($subArea, 1, $length-1, 'ISO-8859-1'),
                        mb_substr($subArea, 0, 1, 'ISO-8859-1')
                    );
                }
            }
            $this->_content = trim(substr($data, 0, $pos));
        } else {
            $this->_content = trim($data);
        }
    }
}
