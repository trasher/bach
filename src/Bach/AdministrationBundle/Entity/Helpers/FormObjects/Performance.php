<?php
/**
 * Performance form object
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
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Entity\Helpers\FormObjects;

use Bach\AdministrationBundle\Entity\SolrPerformance\SolrPerformance;

/**
 * Performance form object
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class Performance
{
    public $queryResultWindowsSize;
    public $documentCacheClass;
    public $documentCacheSize;
    public $documentCacheInitialSize;
    public $queryResultMaxDocsCached;
    public $queryResultClassSize;
    public $queryResultCacheSize;
    public $queryResultInitialCacheSize;
    public $queryResultAutowarmCount;

    private $_xmlp;

    /**
     * Constructor
     *
     * @param XMLProcess $xmlp     XMLProcess instance
     * @param string     $coreName Core name
     */
    public function __construct($xmlp, $coreName = null)
    {
        $this->_xmlp = $xmlp;
        if ($coreName != null) {
            $sp = new SolrPerformance($xmlp, $coreName);
            $this->queryResultWindowsSize = $sp->getQueryResultWindowsSize();
            $parameters = $sp->getDocumentCacheParameters();
            $this->documentCacheClass = $parameters[0];
            $this->documentCacheSize = $parameters[1];
            $this->documentCacheInitialSize = $parameters[2];
            $this->queryResultMaxDocsCached = $sp->getQueryResultMaxDocsCached();
            $parameters = $sp->getQueryResultCacheParameters();
            $this->queryResultClassSize = $parameters[0];
            $this->queryResultCacheSize = $parameters[1];
            $this->queryResultInitialCacheSize = $parameters[2];
            $this->queryResultAutowarmCount = $parameters[3];
        }
    }

    /**
     * Save
     *
     * @param string $coreName Core name
     *
     * @return void
     */
    public function saveAll($coreName)
    {
        $sp = new SolrPerformance($this->_xmlp, $coreName);
        $sp->setDocumentCacheParameters(
            $this->documentCacheClass,
            $this->documentCacheSize,
            $this->documentCacheInitialSize
        );
        $sp->setQueryResultMaxDocsCached($this->queryResultMaxDocsCached);
        $sp->setQueryResultCacheParameters(
            $this->queryResultClassSize,
            $this->queryResultCacheSize,
            $this->queryResultInitialCacheSize,
            $this->queryResultAutowarmCount
        );
        $sp->save();
    }
}
