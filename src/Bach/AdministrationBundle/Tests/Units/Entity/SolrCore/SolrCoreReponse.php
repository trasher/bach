<?php
/**
 * Bach SolrCoreResponse unit tests
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

namespace Bach\AdministrationBundle\Tests\Units\Entity\SolrCore;

use atoum\AtoumBundle\Test\Units;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreResponse as CoreResponse;

/**
 * Bach SolrCoreResponse unit tests
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class SolrCoreResponse extends Units\Test
{

    /**
     * Test basic response
     *
     * @return void
     */
    public function testNewSolrCoreResponse()
    {
        $xml = '<response>
                    <lst name="responseHeader">
                        <int name="status">0</int>
                        <int name="QTime">281</int>
                    </lst>
                    <str name="core">core2</str>
                    <lst name="error">
                        <str name="msg">Error handling "reload" action</str>
                        <str name="trace">org.apache.solr.common.SolrException</str>
                        <int name="code">500</int>
                    </lst>
                </response>';
        $res = new CoreResponse($xml);

        $status = $res->getStatus();
        $this->string($status)->isIdenticalTo('0');

        $message = $res->getMessage();
        $this->string($message)->isIdenticalTo('Error handling "reload" action');

        $trace = $res->getTrace();
        $this->string($trace)->isIdenticalTo('org.apache.solr.common.SolrException');

        $code = $res->getCode();
        $this->string($code)->isIdenticalTo('500');

        $ok = $res->IsOk();
        $this->boolean($ok)->isTrue();
    }
}
