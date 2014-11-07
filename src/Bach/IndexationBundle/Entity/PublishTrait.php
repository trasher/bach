<?php
/**
 * Bach trait for publication
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

/**
 * Bach trait for publication
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 *
 * @ORM\Table(name="ead_header")
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("NOTIFY")
 */
trait PublishTrait
{
    /**
     * Wheter to check for changes or not
     */
    protected $check_changes;

    /**
     * Has current entity changed?
     */
    protected $has_changes;

    /**
      * The constructor
      *
      * @param array   $data    Data
      * @param boolean $changes Take care of changes
      */
    public function __construct($data, $changes = true)
    {
        $this->fragments = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->parseData($data, $changes);
    }

    /**
     * Notifies a property change
     *
     * @param string $propName Property name
     * @param string $oldValue Old value of property
     * @param string $newValue New value of property
     *
     * @return void
     */
    protected function onPropertyChanged($propName, $oldValue, $newValue)
    {
        if ( $this->check_changes === true ) {
            if ( $this->_listeners) {
                foreach ( $this->_listeners as $listener ) {
                    $listener->propertyChanged(
                        $this,
                        $propName,
                        $oldValue,
                        $newValue
                    );
                }
            }
            if ( $propName !== 'updated' ) {
                $now = new \DateTime();
                $this->onPropertyChanged('updated', $this->updated, $now);
                $this->updated = $now;
                $this->has_changes = true;
            }
        }
    }

    /**
     * Hydrate existing entity
     *
     * @param array   $data    Data
     * @param boolean $changes Take care of changes
     *
     * @return void
     */
    public function hydrate($data, $changes = true)
    {
        $this->parseData($data, $changes);
    }

    /**
     * Is current entity changed?
     *
     * @return boolean
     */
    public function hasChanges()
    {
        return $this->has_changes;
    }
}
