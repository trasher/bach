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
    }
}
