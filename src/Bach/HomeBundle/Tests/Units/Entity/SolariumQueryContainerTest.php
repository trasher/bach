<?php
/**
 * Bach SolariumQueryContainer unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
 * @license  Unknown http://unknown.com
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

        $filter_name = $this->faker->name;
        $filter_value = $this->faker->name;

        $filter2_name = $this->faker->name;
        $filter2_value = $this->faker->name;
        $filter3_name = $this->faker->name;
        $filter3_value = $this->faker->name;

        $qc->setFilter($filter3_name, $filter3_value);
        $qc->setFilters(
            array(
                $filter_name    => $filter_value,
                $filter2_name   => $filter2_value
            )
        );

        $filter = $qc->getFilter($filter_name);
        $this->string($filter)->isIdenticalTo($filter_value);

        $filters = $qc->getFilters();

        $attendee_filters = array(
            $filter_name     => $filter_value,
            $filter2_name    => $filter2_value
        );

        $this->array($filters)->isIdenticalTo($attendee_filters);

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

        $this->string($order)->isIdenticalTo('cUnittitle');

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
