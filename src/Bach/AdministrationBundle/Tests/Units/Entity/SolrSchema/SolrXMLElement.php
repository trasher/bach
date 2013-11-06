<?php
/**
 * Bach SolrXMLElement unit tests
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
use Bach\AdministrationBundle\Entity\SolrSchema\SolrXMLElement as Element;
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
class SolrXMLElement extends Units\Test
{

    /**
     * Test construct without name nor value
     *
     * @return void
     */
    public function testNoNameNoValue()
    {
        $elt = new Element(null);

        $actual_name = $elt->getName();
        $actual_value = $elt->getValue();
        $attributes = $elt->getAttributes();
        $elements = $elt->getElements();

        $this->variable($actual_name)->isNull();
        $this->variable($actual_value)->isNull();
        $this->array($attributes)->isEmpty();
        $this->array($elements)->isEmpty();
    }

    /**
     * Test construct with name without value
     *
     * @return void
     */
    public function testNameNoValue()
    {
        $expected = $this->faker->firstName;

        $elt = new Element($expected);

        $actual_name = $elt->getName();
        $actual_value = $elt->getValue();
        $attributes = $elt->getAttributes();
        $elements = $elt->getElements();

        $this->string($actual_name)->isIdenticalTo($expected);
        $this->variable($actual_value)->isNull();
        $this->array($attributes)->isEmpty();
        $this->array($elements)->isEmpty();
    }

    /**
     * Test construct with name and value
     *
     * @return void
     */
    public function testNameValue()
    {
        $expected_name = $this->faker->firstName;
        $expected_value = $this->faker->firstName;

        $elt = new Element($expected_name, $expected_value);

        $actual_name = $elt->getName();
        $actual_value = $elt->getValue();
        $attributes = $elt->getAttributes();
        $elements = $elt->getElements();

        $this->string($actual_name)->isIdenticalTo($expected_name);
        $this->string($actual_value)->isIdenticalTo($expected_value);
        $this->array($attributes)->isEmpty();
        $this->array($elements)->isEmpty();
    }

    /**
     * Test set name
     *
     * @return void
     */
    public function testSetName()
    {
        $expected = $this->faker->firstName;
        $elt = new Element(null);

        $actual_name = $elt->getName();
        $this->variable($actual_name)->isNull();

        $elt->setName($expected);
        $actual_name = $elt->getName();

        $this->string($actual_name)->isIdenticalTo($expected);
    }

    /**
     * Test set value
     *
     * @return void
     */
    public function testSetValue()
    {
        $expected = $this->faker->firstName;
        $elt = new Element($this->faker->firstName);

        $actual_value = $elt->getValue();
        $this->variable($actual_value)->isNull();

        $elt->setValue($expected);
        $actual_value = $elt->getValue();

        $this->string($actual_value)->isIdenticalTo($expected);
    }

    /**
     * Test add attributes
     *
     * @return void
     */
    public function testAddAttributes()
    {
        $expected = $this->_generateAttributes();
        $elt = new Element($this->faker->firstName);

        $actual = $elt->getAttributes();
        $this->array($actual)->isEmpty();

        foreach ( $expected as $att ) {
            $elt->addAttribute($att);
        }
        $actual = $elt->getAttributes();

        $this->array($actual)->isIdenticalTo($expected);
    }

    /**
     * Test get attributes
     *
     * @return void
     */
    public function testGetAttribute()
    {
        $expected = $this->_generateAttributes();
        $elt = new Element($this->faker->firstName);

        $actual = $elt->getAttributes();
        $this->array($actual)->isEmpty();

        foreach ( $expected as $att ) {
            $elt->addAttribute($att);
        }
        $actual = $elt->getAttributes();

        $this->array($actual)->isIdenticalTo($expected);

        $expected = $expected[3];
        $actual = $elt->getAttribute($expected->getName());

        $this->object($actual)->isIdenticalTo($expected);

        $actual = $elt->getAttribute($this->faker->firstName);
        $this->variable($actual)->isNull();
    }

    /**
     * Test set Elements
     *
     * @return void
     */
    public function testSetElements()
    {
        $expected = $this->_generateElements();

        $elt = new Element(
            $this->faker->firstName,
            $this->faker->name
        );

        $elt->setElements($expected);

        $actual = $elt->getElements();

        $this->array($actual)->isIdenticalTo($expected);
    }

    /**
     * Test add Elements
     *
     * @return void
     */
    public function testAddElements()
    {
        $expected = $this->_generateElements(1);

        $elt = new Element(
            $this->faker->firstName,
            $this->faker->name
        );

        $elt->addElement($expected[0]);

        $actual = $elt->getElements();

        $this->array($actual)->isIdenticalTo($expected);
    }

    /**
     * Test get Elements by name
     *
     * @return void
     */
    public function testGetElementsByname()
    {
        $expected = $this->_generateElements(3);

        $elt = new Element(
            $this->faker->firstName,
            $this->faker->name
        );

        $elt->setElements($expected);
        $actual = $elt->getElements();

        $this->array($actual)->isIdenticalTo($expected);

        $single_expected = $expected[0];
        $single_expected_mod = $single_expected;
        $single_expected_mod->setValue($this->faker->name);
        $elt->addElement($single_expected_mod);

        $actual = $elt->getElementsByname($single_expected->getName());

        $this->array($actual)->hasSize(2)
            ->strictlyContains($single_expected)
            ->strictlyContains($single_expected_mod);
    }

    /**
     * Generate attributes into Element
     *
     * @param int $count Number of required attributes, defaults to 10
     *
     * @return Attributes[]
     */
    private function _generateAttributes($count = 10)
    {
        $attributes = array();

        for ($i = 0; $i < $count; $i++ ) {
            $attribute = new Attribute(
                $this->faker->firstName,
                $this->faker->name
            );
            $attributes[] = $attribute;
        }

        return $attributes;
    }

    /**
     * Generate Elements
     *
     * @param int $count Number of required elements, defaults to 5
     *
     * @return Elements[]
     */
    private function _generateElements($count = 5)
    {
        $elements = array();

        for ($i = 0; $i < $count; $i++ ) {
            $elt = new Element(
                $this->faker->firstName,
                $this->faker->name
            );
            $attributes = $this->_generateAttributes(2);
            foreach ( $attributes as $attr ) {
                $elt->addAttribute($attr);
            }
            $elements[] = $elt;
        }

        return $elements;
    }
}
