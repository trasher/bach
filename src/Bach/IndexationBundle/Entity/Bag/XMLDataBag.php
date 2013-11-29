<?php
/**
 * Bach XML data bag
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

namespace Bach\IndexationBundle\Entity\Bag;

use Bach\IndexationBundle\Entity\DataBag;

/**
 * Bach text data bag
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class XMLDataBag extends DataBag
{
    /**
     * The constructor
     *
     * @param SplFileInfo $fileInfo The input file
     */
    public function __construct(\SplFileInfo $fileInfo)
    {
        $this->type = "xml";
        $this->fileInfo = $fileInfo;
        $dom = new \DOMDocument();
        $dom->load($fileInfo->getRealPath());
        $this->data = $dom;
    }
}
