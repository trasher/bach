<?php

/**
 * Put input file in appropriate DataBag
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

namespace Bach\IndexationBundle\Service;

use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Entity\Bag\TextDataBag;
use Bach\IndexationBundle\Entity\Bag\XMLDataBag;

/**
 * Put input file in appropriate DataBag
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <uknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DataBagFactory
{
    /**
     * Put input file in appropriate DataBag
     *
     * @param SplFileInfo $fileInfo FilesController
     *
     * @return DataBag
     */
    public function encapsulate(\SplFileInfo $fileInfo)
    {
        switch ($fileInfo->getExtension()) {
        default:
        case 'txt':
            return new TextDataBag($fileInfo);
            break;
        case 'xml':
            return new XMLDataBag($fileInfo);
            break;
        }
    }
}
