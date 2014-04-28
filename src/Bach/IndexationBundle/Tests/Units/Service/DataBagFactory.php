<?php
/**
 * Bach DataBagFactory unit tests
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

namespace Bach\IndexationBundle\Tests\Units\Service;

use Bach\IndexationBundle\Service\DataBagFactory as Factory;
use Bach\IndexationBundle\Entity\Bag\XMLDataBag;
use atoum\AtoumBundle\Test\Units;

/**
 * Bach DataBagFactory unit tests
 *
 * PHP version 5
 *
 * @category Tests
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class DataBagFactory extends Units\Test
{
    /**
     * Tests service instanciation
     *
     * @return void
     */
    public function testService()
    {
        $xml_str = '<?xml version="1.0"?><data>' .
            '<address loc="Paris"></address></data>';

        $xml = simplexml_load_string($xml_str);
        $tmpName = uniqid();

        $tmp_dir =  sys_get_temp_dir() . '/bach_tests_tmp/';
        if ( !file_exists($tmp_dir) ) {
            mkdir($tmp_dir);
        }

        $xml->asXML($tmp_dir . $tmpName . '.xml');

        $fileInfo = new \SplFileInfo(
            $tmp_dir . $tmpName . '.xml'
        );

        $factory = new Factory();
        $result = $factory->encapsulate($fileInfo);

        $is_instance = $result instanceof XMLDataBag;
        $this->boolean($is_instance)->isTrue();

        unlink($fileInfo->getRealPath());
    }
}
