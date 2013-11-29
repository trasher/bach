<?php
/**
 * Bach text data bag
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
class TextDataBag extends DataBag
{
    /**
     * The constructor
     *
     * @param SplFileInfo $fileInfo The input file
     */
    public function __construct(\SplFileInfo $fileInfo)
    {
        $this->type = "txt";
        $this->fileInfo = $fileInfo;
        $this->data = file_get_contents($fileInfo->getRealPath());
    }
}
