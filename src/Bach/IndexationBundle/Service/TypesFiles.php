<?php
/**
 * Files to publish
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

use Symfony\Component\Finder\Finder;

/**
 * Files to publish
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class TypesFiles
{
    private $_formats;
    private $_paths;

    /**
     * Main constructor
     *
     * @param array $formats List of known types
     * @param array $paths   List of types files paths
     */
    public function __construct($formats, $paths)
    {
        $this->_formats = $formats;
        $this->_paths = $paths;
    }

    /**
     * Retrieve existing files list
     *
     * @param string|array $format Type of files to retrieve
     * @param string|array $paths  Directories or files to limit on
     *
     * @return Iterator
     */
    public function getExistingFiles($format = null, $paths = null)
    {
        $existing_files = array();

        if ( $format !== null ) {
            if ( !is_array($format) ) {
                $this->_formats = array($format);
            } else {
                $this->_formats = $format;
            }
        }

        if ( $paths !== null ) {
            if ( !is_array($paths) ) {
                $paths = array($paths);
            }
        }

        foreach ( $this->_formats as $format ) {
            $finder = new Finder();
            $finder->followLinks()
                ->ignoreDotFiles(true)
                ->ignoreVCS(true)
                -> ignoreUnreadableDirs(true)
                ->sortByType();

            $path = $this->_paths[$format];
            if ( $paths === null ) {
                if ( file_exists($path) && is_dir($path) ) {
                    $existing_files[$format] = $this->_parseExistingFiles(
                        $finder->files()->in($path)
                    );
                }
            } else {
                $found_files = array();
                foreach ( $paths as $p ) {
                    if ( strpos($p, '/') === 0 ) {
                        $p = ltrim($p, '/');
                    }
                    if ( file_exists($path . $p) ) {
                        if ( is_dir($path . $p) ) {
                            $files = $this->_parseExistingFiles(
                                $finder->files()->in($path . $p),
                                $path . $p
                            );
                            $found_files = array_merge(
                                $found_files,
                                $files
                            );
                        } else {
                            $found_files = array_merge(
                                $found_files,
                                array($path . $p)
                            );
                        }
                    } else {
                        throw new \RuntimeException(
                            str_replace(
                                '%path',
                                $path . $p,
                                _('File or directory %path does not exists!')
                            )
                        );
                    }
                }
                $existing_files[$format] = $found_files;
            }
        }

        return $existing_files;
    }

    /**
     * Parse existing files into an array for display
     *
     * @param Iterator $finder Finder results iterator
     * @param string   $path   Parent path to prepend
     *
     * @return array
     */
    private function _parseExistingFiles($finder, $path = null)
    {
        $existing_files = array();
        foreach ( $finder as $found ) {
            if ( !$found->isDir() ) {
                $parent = $found->getRelativePath();
                $filename = $found->getFileName();

                if ( $parent !== '' ) {
                    $filename = $parent . '/' . $filename;
                }

                if ( $path !== null ) {
                    if ( substr($path, -1) !== '/' ) {
                        $path .= '/';
                    }
                    $filename = $path . $filename;
                }

                $existing_files[] = $filename;
            }
        }
        return $existing_files;
    }
}
