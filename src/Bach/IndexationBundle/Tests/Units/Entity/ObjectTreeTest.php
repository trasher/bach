<?php
/**
 * Bach ObjectTree unit tests
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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

        $is_instance_true = $tree->get('child')
            ->get('bar')->getContent() instanceof Bar;
        $is_instance_false = $tree->get('child')
            ->get('bar')->getContent() instanceof Foo;

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

/**
 * Bach lambda foo class
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Foo
{
}

/**
 * Bach lambda bar class
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Bar
{
}
