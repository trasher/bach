<?php
/**
 * Bach Filters unit tests
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
use Bach\HomeBundle\Entity\Filters as Entity;

/**
 * Bach Filters unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Filters extends Units\WebTestCase
{
    /**
     * Test add filter
     *
     * @return void
     */
    public function testFilters()
    {
        $filters = new Entity();

        $client = static::createClient();
        $client->request(
            'POST',
            '/placebo',
            array(
                'filter_field' => 'cSubject',
                'filter_value' => 'édifice de culte'
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $expected = (array)new\ArrayObject(
            array('édifice de culte')
        );

        $ao = (array)$filters->offsetGet('cSubject');
        $this->array($ao)->isIdenticalTo($expected);

        $client->request(
            'POST',
            '/placebo',
            array(
                'filter_field' => array(
                    'cSubject',
                    'cSubject',
                    'cGeogname'
                ),
                'filter_value' => array(
                    'place',
                    'mairie',
                    'Avignon'
                )
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $count = $filters->count();
        //2 fields are filtered
        $this->integer($count)->isIdenticalTo(2);

        $expected = (array)new\ArrayObject(
            array(
                'édifice de culte',
                'place',
                'mairie'
            )
        );

        $ao = (array)$filters->offsetGet('cSubject');
        $this->array($ao)->hasSize(3)->isIdenticalTo($expected);

        $expected = (array)new\ArrayObject(
            array('Avignon')
        );

        $ao = (array)$filters->offsetGet('cGeogname');
        $this->array($ao)->hasSize(1)->isIdenticalTo($expected);

        //test remove one of 3 filters
        $client->request(
            'POST',
            '/placebo',
            array(
                'rm_filter_field' => 'cSubject',
                'rm_filter_value' => 'place'
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $expected = (array)new\ArrayObject(
            array(
                'édifice de culte',
                'mairie'
            )
        );

        $ao = (array)$filters->offsetGet('cSubject');
        $this->array($ao)->hasSize(2)->strictlyContainsValues($expected);

        //test remove one of last filter
        $client->request(
            'POST',
            '/placebo',
            array(
                'rm_filter_field' => 'cGeogname',
                'rm_filter_value' => 'Avignon'
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $exists = $filters->offsetExists('cGeogname');
        $this->boolean($exists)->isFalse();

        //test remove one of non existant filter
        $client->request(
            'POST',
            '/placebo',
            array(
                'rm_filter_field' => 'cSubject',
                'rm_filter_value' => 'doesnotexists'
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $ao = (array)$filters->offsetGet('cSubject');
        $this->array($ao)->hasSize(2)->strictlyContainsValues($expected);

        $client->request(
            'POST',
            '/placebo',
            array(
                'filter_field' => array(
                    'cSubject'
                ),
                'filter_value' => array(
                    'place',
                    'mairie'
                )
            )
        );
        $req = $client->getRequest();

        $this->exception(
            function () use ($filters, $req) {
                $filters->bind($req);
            }
        );

        $client->request(
            'POST',
            '/placebo',
            array(
                'filter_field' => array(
                    'date_begin',
                    'date_end',
                    'dao'
                ),
                'filter_value' => array(
                    '2013',
                    '2013',
                    'true'
                )
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $bdate = $filters->offsetGet('date_begin');
        $edate = $filters->offsetGet('date_end');
        $dao = $filters->offsetGet('dao');
        $bexpected = '2013-01-01';
        $eexpected = '2013-12-31';

        $this->string($bdate)->isIdenticalTo($bexpected);
        $this->string($edate)->isIdenticalTo($eexpected);
        $this->string($dao)->isIdenticalTo('true');

        $client->request(
            'POST',
            '/placebo',
            array(
                'rm_filter_field' => 'cDateEnd',
                'rm_filter_value' => '2013'
            )
        );
        $req = $client->getRequest();
        $filters->bind($req);

        $exists = $filters->offsetExists('cDateEnd');
        $this->boolean($exists)->isFalse();
    }
}
