<?php

/**
 * Pre processor factory
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
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Service;

use Bach\IndexationBundle\Entity\PreProcessor\XSLTPreProcessor;
use Bach\IndexationBundle\Entity\PreProcessor\JavaPreProcessor;
use Bach\IndexationBundle\Entity\PreProcessor\PHPPreProcessor;
use Bach\IndexationBundle\Entity\DataBag;


/**
 * Pre processor factory
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <unknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class PreProcessorFactory
{
    private $_dataBagFactory;

    /**
     * Constructor
     *
     * @param DataBagFactory $factory Data bag factory
     */
    public function __construct(DataBagFactory $factory)
    {
        $this->_dataBagFactory = $factory;
    }

    /**
     * Pre process data
     *
     * @param DataBag $fileBag           Data bag to process
     * @param string  $processorFilename Processor name
     *
     * @return mixed
     */
    public function preProcess(DataBag $fileBag, $processorFilename)
    {
        $spl = new \SplFileInfo($processorFilename);
        if ($spl->isFile()) {
            switch($spl->getExtension())
            {
            case 'xsl':
                $processor = new XSLTPreProcessor($this->_dataBagFactory);
                break;
            case 'java':
                $processor = new JavaPreProcessor($this->_dataBagFactory);
                break;
            case 'php':
                $processor = new PHPPreProcessor($this->_dataBagFactory);
                break;
            }

            if ( !is_null($processor) ) {
                return $processor->process($fileBag, $spl);
            } else {
                return $fileBag;
            }
        } else {
            return $fileBag;
        }
    }
}
