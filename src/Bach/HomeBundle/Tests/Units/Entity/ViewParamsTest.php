<?php
/**
 * Bach ViewParams unit tests
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
use Bach\HomeBundle\Entity\ViewParams as Vp;

/**
 * Bach ViewParams unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class ViewParams extends Units\WebTestCase
{
    /**
     * Test view parameters
     *
     * @return void
     */
    public function testViewParams()
    {
        $vp = new Vp();

        //test default values
        $show_pics = $vp->showPics();
        $res_per_page = $vp->getResultsByPage();
        $view = $vp->getView();
        $order = $vp->getOrder();

        $this->boolean($show_pics)->isTrue();
        $this->variable($res_per_page)->isIdenticalTo(10);
        $this->string($view)->isIdenticalTo(Vp::VIEW_LIST);
        $this->variable($order)->isIdenticalTo(Vp::ORDER_RELEVANCE);

        $client = static::createClient();
        $client->request(
            'POST',
            '/placebo',
            array(
                'view'              => Vp::VIEW_THUMBS,
                'results_by_page'   => 13,
                'results_order'     => 1
            )
        );
        $req = $client->getRequest();
        $vp->bind($req);

        $res_per_page = $vp->getResultsByPage();
        $view = $vp->getView();
        $order = $vp->getOrder();

        $this->variable($res_per_page)->isIdenticalTo(13);
        $this->string($view)->isIdenticalTo(Vp::VIEW_THUMBS);
        $this->variable($order)->isIdenticalTo(Vp::ORDER_TITLE);

        $this->exception(
            function () use ( $vp ) {
                $vp->setView('doesnotexists');
            }
        )->hasMessage('View doesnotexists is not known!');

        $this->exception(
            function () use ( $vp ) {
                $vp->setOrder('doesnotexists');
            }
        )->hasMessage('Order doesnotexists is not known!');

        /** FIXME: this one works in regular app, but not in tests :/ */
        /*$client->request(
            'POST',
            '/placebo',
            array(
                'view'          => Vp::VIEW_TEXT_LIST
            )
        );
        $req = $client->getRequest();
        $vp->bind($req);

        $this->string($view)->isIdenticalTo(Vp::VIEW_TEXT_LIST);*/
    }
}
