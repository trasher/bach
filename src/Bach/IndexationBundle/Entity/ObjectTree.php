<?php
/**
 * Object tree
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
 * Object tree
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ObjectTree implements ObjectTreeComponentInterface
{
    private $_sheets = array();
    private $_children = array();
    private $_name;

    /**
     * The constructor
     *
     * @param string $name The name of the tree
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    /**
     * Add a component to the tree
     *
     * @param ObjectTreeComponentInterface $sheet The component to add
     *
     * @return void
     */
    public function append(ObjectTreeComponentInterface $sheet)
    {
        if ($sheet instanceof ObjectTree) {
            if (array_key_exists($sheet->getName(), $this->_children)
                || array_key_exists($sheet->getName(), $this->_sheets)
            ) {
                throw new \RuntimeException("ObjectTree sheet conflict name");
            } else {
                $this->_children[$sheet->getName()] = $sheet;
            }
        } elseif ($sheet instanceof ObjectSheet) {
            if (array_key_exists($sheet->getName(), $this->_children)
                || array_key_exists($sheet->getName(), $this->_sheets)
            ) {
                throw new \RuntimeException("ObjectTree sheet conflict name");
            } else {
                $this->_sheets[$sheet->getName()] = $sheet;
            }
        }
    }

    /**
     * Retrieve a component from the tree
     *
     * @param string $name The name of the component
     *
     * @return ObjectTree|ObjectSheet The tree component
     */
    public function get($name)
    {
        if ( isset($this->_children[$name]) ) {
            return $this->_children[$name];
        }

        if ( isset($this->_sheets[$name]) ) {
            return $this->_sheets[$name];
        }

        return false;
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
}
