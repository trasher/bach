<?php
/**
 * File driver abstract class
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Anph\IndexationBundle\Entity;

/**
 * File driver abstract class
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
abstract class FileDriver
{
    protected $configuration = array();

    /**
     * The constructor
     *
     * @param array $configuration The driver's configuration
     */
    public function __construct($configuration = array())
    {
        $this->configuration = $configuration;
    }

    /**
     * Perform the parsing of the DataBag
     *
     * @param DataBag $bag The data
     *
     * @return array
     */
    abstract public function process(DataBag $bag);

    /**
     * Get driver format name
     *
     * @return string $format The format of the driver
     *
     * @return stirng
     */
    abstract public function getFileFormatName();
}
