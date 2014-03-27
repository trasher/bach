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
class UniversalFileFormatFactory
{
    /**
     * Builds
     *
     * @param array  $data   Data
     * @param string $class  Class name
     * @param object $exists Existing object, if any
     *
     * @return UniversalFileFormat
     */
    public function build($data, $class, $exists)
    {
        if ( !$exists ) {
            if (class_exists($class)) {
                $universal = new $class($data);
            } else {
                throw new \RuntimeException(
                    'File format class ' . $class . ' does not exists.'
                );
            }
        } else {
            $universal = $exists;
            $universal->hydrate($data);
        }

        return $universal;
    }
}

