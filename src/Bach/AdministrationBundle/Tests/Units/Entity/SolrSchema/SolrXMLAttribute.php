<?php
/**
 * Bach SolrXMLAttribute unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
 * @license  Unknown http://unknown.com
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
