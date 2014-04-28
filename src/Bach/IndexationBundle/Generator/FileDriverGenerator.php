<?php
/**
 * Bach file driver generator
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

namespace Bach\IndexationBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Bach file driver generator
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class FileDriverGenerator extends Generator
{
    private $_filesystem;
    private $_skeletonDir;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem  ?
     * @param string     $skeletonDir Skeleton storage directory
     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->_filesystem = $filesystem;
        $this->_skeletonDir = $skeletonDir;
    }

    /**
     * Generate driver mapper
     *
     * @param string $namespace Namespace
     * @param string $format    Format
     * @param string $datatype  Data type
     *
     * @return void
     */
    public function generate($namespace, $format, $datatype)
    {
        $dir = __DIR__.'/../Entity/Driver';

        if (!file_exists($dir.'/'.strtoupper($format))) {
            mkdir($dir.'/'.strtoupper($format), 0755, true);
        }

        $dir = $dir.'/'.strtoupper($format);

        $parameters = array(
            'namespace'         => $namespace,
            'format'            => $format,
            'format_uppercase'  => strtoupper($format)
        );

        if (!file_exists($dir.'/Driver.php')) {
            $this->renderFile(
                $this->_skeletonDir,
                'Driver.php',
                $dir.'/Driver.php',
                $parameters
            );
        }

        $parserDir = $dir.'/Parser';

        $parameters = array(
            'namespace' => $namespace.'\Parser\\'.strtoupper($datatype)
        );

        if (!file_exists($parserDir.'/'.strtoupper($datatype))) {
            mkdir($parserDir . '/' . strtoupper($datatype), 0755, true);
            $this->renderFile(
                $this->_skeletonDir,
                'Parser.php',
                $parserDir.'/'.strtoupper($datatype).'/Parser.php',
                $parameters
            );
        }
    }
}
