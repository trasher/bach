<?php
/**
 * Bach abstract solarium query decorator
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
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Solarium\QueryType\Select\Query\Query;

/**
 * Bach abstract solarium query decorator
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
abstract class SolariumQueryDecoratorAbstract
{
    protected $targetField;
    protected $qf;

    /**
     * Main constructor
     *
     * @param string $qf Query fields to override defaults
     */
    public function __construct($qf = null)
    {
        if ( $qf !== null ) {
            $this->setQueryFields($qf);
        }
    }

    /**
     * Get target field
     *
     * @return string
     */
    public function getTargetField()
    {
        return $this->targetField;
    }

    /**
     * Return query fields
     *
     * @return string
     */
    public function getQueryFields()
    {
        if ( $this->qf !== null ) {
            return $this->qf;
        } else {
            return $this->getDefaultQueryFields();
        }
    }

    /**
     * Set query fields
     *
     * @param string $qf Query fields and boost
     *
     * @return void
     */
    public function setQueryFields($qf)
    {
        $this->qf = $qf;
    }

    /**
     * Default query fields and boost
     *
     * @return string
     */
    protected function getDefaultQueryFields()
    {
        //default empty
    }

    /**
     * Decorate query
     *
     * @param Query $query Solarium query to decorate
     * @param array $data  Query data
     *
     * @return void
     */
    abstract public function decorate(Query $query, $data);
}
?>
