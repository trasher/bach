<?php
/**
 * Bach default controller unit tests
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

namespace Bach\HomeBundle\Tests\Controller;

use atoum\AtoumBundle\Test\Units\WebTestCase;
use atoum\AtoumBundle\Test\Controller\ControllerTest;

/**
 * Bach default controller unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DefaultController extends ControllerTest
{
    /**
     * Test index action
     *
     * @return void
     */
    public function testIndex()
    {
        $this->request()->GET('/search/')
            ->hasStatus(301);

        $this->request()->GET('/notfound')
            ->hasStatus(404);

        $this->request()->GET('/')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#tagCloud');

        //those ones will work with FRANCT_0001 EAD document indexed...
        //a successfull request
        $this->request->GET('/search/Cayenne')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('article');

        //a not successfull request, with one spelling suggestion
        //FIXME: the french text will fail if app is in english...
        //We should find a fix for that.
        $this->request->GET('/search/cyenne')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('p')
            ->withContent('No result found.')
            ->end()
            ->hasChild('#suggestions')
            ->hasChild('a')
            ->withContent('cayenne');

        //a successfull request, with filtering only
        $this->request->GET(
            '/search?filter_field=cSubject&filter_value=enjeux+internationaux'
        )
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#active_filters')
            ->hasChild('ul')->exactly(1)
            ->end()->end()
            ->hasElement('#search_results')
            ->hasChild('article');

        $this->request->GET('/search/Cayenne?view=thumbs')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('article.thumbs');

        $this->request->GET('/search/Cayenne?result_order=1')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#search_results')
            ->hasChild('article');
    }

    /**
     * test facet listing
     *
     * @return void
     */
    public function testFacetList()
    {
        $this->request->GET('/fullfacet/*:*/cSubject')
            ->hasStatus(200)
            ->hasCharset('UTF-8')
            ->crawler
            ->hasElement('#facets_list');
    }
}
