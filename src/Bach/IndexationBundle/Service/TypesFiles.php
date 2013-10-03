<?php
/**
 * Files to publish
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
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
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class TypesFiles
{
    private $_formats;
    private $_paths;
    private $_dirs;

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
                $existing_files[$format] = $this->_parseExistingFiles(
                    $finder->files()->in($path)
                );
            } else {
                $found_files = array();
                foreach ( $paths as $p ) {
                    if ( file_exists($path . $p) ) {
                        if ( is_dir($path . $p) ) {
                            $files = $this->_parseExistingFiles(
                                $finder->files()->in($path . $p)
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
                                $p,
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
     *
     * @return array
     */
    private function _parseExistingFiles($finder)
    {
        $existing_files = array();
        foreach ( $finder as $found ) {
            if ( !$found->isDir() ) {
                $parent = $found->getRelativePath();
                if ( $parent !== '' ) {
                    $existing_files[$parent][] = $found->getFileName();
                } else if ( $found->getRealPath() !== false ) {
                    $existing_files[] = $found->getFileName();
                }
            }
        }
        return $existing_files;
    }
}
