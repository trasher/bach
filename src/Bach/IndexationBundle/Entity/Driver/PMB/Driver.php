<?php
/**
 * PMB file format driver
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
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Entity\Driver\PMB;

use Bach\IndexationBundle\Entity\FileDriver;
use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Entity\ObjectTree;
use Bach\IndexationBundle\Exception\UnknownDriverParserException;

/**
 * PMB file format driver
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Driver extends FileDriver
{

    /**
     * Perform the parsing of the DataBag
     *
     * @param DataBag $bag The data
     *
     * @return array
     */
    public function process(DataBag $bag)
    {
        $parserClass = 'Bach\IndexationBundle\Entity\Driver\PMB\Parser\\'.
            strtoupper($bag->getType()) . '\Parser';

        if (!class_exists($parserClass)) {
            throw new UnknownDriverParserException(strtoupper($bag->getType()));
        }

        $parser = new $parserClass($bag, $this->configuration);
        $tree = $parser->getTree();
        return $this->_processTree($tree);
    }

    /**
     * Get driver format name
     *
     * @return string $format The format of the driver
     *
     * @return stirng
     */
    public function getFileFormatName()
    {
        return 'pmb';
    }

    /**
     * Process the object tree returned by the parser
     *
     * @param ObjectTree $tree The parser's tree
     *
     * @return array Data parsed
     */
    private function _processTree(ObjectTree $tree)
    {
        $results = array();

        $pmbs = $tree->get('notices')->getContent();
        foreach ( $pmbs as $pmb ) {
            $results[] = $pmb->getValues();
        }

        return $results;
    }

}
