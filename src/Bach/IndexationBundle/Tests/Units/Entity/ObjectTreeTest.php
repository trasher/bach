<?php
/**
 * Bach ObjectTree unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Tests\Units\Entity;

use atoum\AtoumBundle\Test\Units;
use Bach\IndexationBundle\Entity\ObjectTree as Ot;
use Bach\IndexationBundle\Entity\ObjectSheet;

/**
 * Bach ObjectTree unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ObjectTree extends Units\Test
{

    /**
     * Test ObjectTree
     *
     * @return void
     */
    public function testTree()
    {
        $tree = new Ot("root");

        $tree->append(
            new ObjectSheet('foo', new Foo())
        );
        $tree->append(
            new Ot('child')
        );
        $tree->get('child')->append(
            new ObjectSheet('bar', new Bar())
        );

        $is_instance_true = $tree->get('foo')->getContent() instanceof Foo;
        $is_instance_false = $tree->get('foo')->getContent() instanceof Bar;

        $this->boolean($is_instance_true)->isTrue();
        $this->boolean($is_instance_false)->isFalse();

        $is_instance_true = $tree->get('child')->get('bar')->getContent() instanceof Bar;
        $is_instance_false = $tree->get('child')->get('bar')->getContent() instanceof Foo;

        $this->boolean($is_instance_true)->isTrue();
        $this->boolean($is_instance_false)->isFalse();

        $is_instance_true = $tree->get('child') instanceof Ot;
        $this->boolean($is_instance_true)->isTrue();

        $root_name = $tree->getName();

        $this->string($root_name)->isIdenticalTo('root');

        $this->exception(
            function () use ( $tree ) {
                $tree->get('child')->append(
                    new ObjectSheet('bar', new Foo())
                );
            }
        )->hasMessage('ObjectTree sheet conflict name');

        $this->exception(
            function () use ( $tree ) {
                $tree->append(
                    new Ot('child')
                );
            }
        )->hasMessage('ObjectTree sheet conflict name');

        $get_false = $tree->get('doesnotexists');
        $this->boolean($get_false)->isFalse();
    }
}

class Foo{}
class Bar{}
