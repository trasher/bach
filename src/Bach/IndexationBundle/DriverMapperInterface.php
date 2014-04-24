<?php

/**
 * Driver mapper interface
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle;


/**
 * DriverManager convert an input file into a FileFormat object
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
interface DriverMapperInterface
{
    /**
     * Translate the input data
     *
     * @param array $data The input data
     *
     * @return array Translated data
     */
    public function translate($data);
}
