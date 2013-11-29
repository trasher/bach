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

use Bach\IndexationBundle\Entity\UniversalFileFormat;

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
     * @param array  $data  Data
     * @param string $class Class name
     *
     * @return UniversalFileFormat
     */
    public function build($data, $class)
    {
        if (class_exists($class)) {
            $universal = new $class($data);
        } else {
            $universal = new UniversalFileFormat($data);
        }

        return $universal;
    }
}

