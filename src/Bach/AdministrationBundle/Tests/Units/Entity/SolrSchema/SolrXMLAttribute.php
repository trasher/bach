<?php
/**
 * Bach SolrXMLAttribute unit tests
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
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrSchema;

use atoum\AtoumBundle\Test\Units;
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute as Attribute;

/**
 * Bach SolrXMLAttribute unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolrXMLAttribute extends Units\Test
{

    /**
     * Test attribute without name, without value
     *
     * @return void
     */
    public function testCreateNoNameNoValue()
    {
        $this->exception(
            function () {
                new Attribute(null);
            }
        )->hasMessage('SolrXMLAttribute must be instanciated with a name!');
    }

    /**
     * Test attribute with name, without value
     *
     * @return void
     */
    public function testCreateNameNoValue()
    {
        $expected = $this->faker->name;

        $attribute = new Attribute($expected);

        $actual_name = $attribute->getName();
        $actual_value = $attribute->getValue();

        $this->string($actual_name)->isIdenticalTo($expected);
        $this->variable($actual_value)->isNull();
    }

    /**
     * Test attribute with name and value
     *
     * @return void
     */
    public function testCreateNameAndValue()
    {
        $expected_name = $this->faker->name;
        $expected_value = $this->faker->name;

        $attribute = new Attribute($expected_name, $expected_value);

        $actual_name = $attribute->getName();
        $actual_value = $attribute->getValue();

        $this->string($actual_name)->isIdenticalTo($expected_name);
        $this->string($actual_value)->isIdenticalTo($expected_value);
    }

    /**
     * Test set value
     *
     * @return void
     */
    public function testSetValue()
    {
        $expected = $this->faker->name;

        $attribute = new Attribute($this->faker->name);

        $actual = $attribute->getValue();
        $this->variable($actual)->isNull();

        $attribute->setValue($expected);
        $actual = $attribute->getValue();

        $this->string($actual)->isIdenticalTo($expected);
    }
}
