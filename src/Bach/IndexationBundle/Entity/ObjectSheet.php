<?php
/**
 * Object sheet
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity;

use Bach\IndexationBundle\ObjectTreeComponentInterface;

/**
 * Object sheet
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ObjectSheet implements ObjectTreeComponentInterface
{
    private $_name;
    private $_content;

    /**
     * The constructor
     *
     * @param string $name    The name of the sheet
     * @param mixed  $content The content of the sheet
     */
    public function __construct($name, $content)
    {
        $this->_name = $name;
        $this->_content = $content;
    }

    /**
     * Get name
     *
     * @return string The name of the sheet
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get content
     *
     * @return mixed The content of the sheet
     */
    public function getContent()
    {
        return $this->_content;
    }
}
