<?php
/**
 * FORMAT file format driver
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

namespace Bach\IndexationBundle\Entity\Driver\FORMAT;

use Bach\IndexationBundle\Entity\FileDriver;
use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Entity\ObjectTree;
use Bach\IndexationBundle\Exception\UnknownDriverParserException;

/**
 * FORMAT file format driver
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class Driver extends FileDriver
{

    /**
     * Perform the parsing of the DataBag
     *
     * @param DataBag $bag The data
     *
     * @return array
     */
    public function process(DataBag $bag)
    {
        $parserClass = 'Bach\IndexationBundle\Entity\Driver\FORMAT_UPPERCASE\Parser\\'.
            strtoupper($bag->getType()) . '\Parser';

        if (!class_exists($parserClass)) {
            throw new UnknownDriverParserException(strtoupper($bag->getType()));
        }

        $parser = new $parserClass($bag, $this->configuration);
        $tree = $parser->getTree();
        return $this->_processTree($tree);
    }

    /**
     * Get driver format name
     *
     * @return string $format The format of the driver
     *
     * @return stirng
     */
    public function getFileFormatName()
    {
        return "FORMAT";
    }

    /**
     * Process the object tree returned by the parser
     *
     * @param ObjectTree $tree The parser's tree
     *
     * @return array Data parsed
     */
    private function _processTree(ObjectTree $tree)
    {
        $results = array();

        return $results;
    }

}
