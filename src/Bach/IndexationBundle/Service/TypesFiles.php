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
     *
     * @return Iterator
     */
    public function getExistingFiles($format = null)
    {
        $existing_files = array();

        if ( $format !== null ) {
            if ( !is_array($format) ) {
                $this->_formats = array($format);
            } else {
                $this->_formats = $format;
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
            $existing_files[$format] = $this->_parseExistingFiles(
                $finder->files()->in($path)
            );
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
