<?php
/**
 * File format factory
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

/**
 * File format factory
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class FileFormatFactory
{
    /**
     * Builds
     *
     * @param array  $data   Data
     * @param string $class  Class name
     * @param object $exists Existing object, if any
     *
     * @return FileFormat
     */
    public function build($data, $class, $exists)
    {
        if ( !$exists ) {
            if (class_exists($class)) {
                $fileformat = new $class($data);
            } else {
                throw new \RuntimeException(
                    'File format class ' . $class . ' does not exists.'
                );
            }
        } else {
            $fileformat = $exists;
            $fileformat->hydrate($data);
        }

        return $fileformat;
    }
}

