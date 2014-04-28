<?php
/**
 * Object tree
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
