<?php
/**
 * Bach SolariumQueryContainer unit tests
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

namespace Bach\HomeBundle\Tests\Units\Entity;

use atoum\AtoumBundle\Test\Units;
use Bach\HomeBundle\Entity\SolariumQueryContainer as Container;
use Bach\HomeBundle\Entity\ViewParams;

/**
 * Bach SolariumQueryContainer unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolariumQueryContainer extends Units\Test
{
    /**
     * Test query container fields
     *
     * @return void
     */
    public function testQueryContainerFields()
    {
        $qc = new Container();

        $field_name = $this->faker->name;
        $field_value = $this->faker->name;

        $field2_name = $this->faker->name;
        $field2_value = $this->faker->name;

        $qc->setField($field_name, $field_value);
        $qc->setField($field2_name, $field2_value);

        $has_field_true = $qc->hasField($field_name);
        $has_field_false = $qc->hasField($this->faker->name);

        $this
            ->boolean($has_field_true)->isTrue()
            ->boolean($has_field_false)->isFalse();

        $field = $qc->getField($field_name);
        $this->string($field)->isIdenticalTo($field_value);

        $fields = $qc->getFields();

        $attendee_fields = array(
            $field_name     => $field_value,
            $field2_name    => $field2_value
        );

        $this->array($fields)->isIdenticalTo($attendee_fields);
    }

    /**
     * Test query container filters
     *
     * @return void
     */
    public function testQueryContainerFilters()
    {
        $qc = new Container();

        $ordered = $qc->isOrdered();
        $this->boolean($ordered)->isFalse();

        //test order
        $attendee_order = $this->faker->name;
        $qc->setOrder($attendee_order);
        $order = $qc->getOrderField();

        $this->variable($order)->isIdenticalTo($attendee_order);

        $ordered = $qc->isOrdered();
        $this->boolean($ordered)->isTrue();

        //test order on known fields
        $qc->setOrder(ViewParams::ORDER_TITLE);
        $order = $qc->getOrderField();

        $this->string($order)->isIdenticalTo('ocUnittitle');

        $qc->setOrder(ViewParams::ORDER_DOC_LOGIC);
        $attendee_orders = array(
            'archDescUnitTitle',
            'elt_order'
        );

        $order = $qc->getOrderField();

        $this->array($order)->isIdenticalTo($attendee_orders);

        $attendee_direction = ViewParams::ORDER_ASC;
        $direction = $qc->getOrderDirection();

        $this->variable($direction)->isIdenticalTo($attendee_direction);
    }
}
