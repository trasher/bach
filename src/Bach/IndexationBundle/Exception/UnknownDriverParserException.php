<?php
/**
 * Bach unknown driver parser Exception
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Exception;

use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * Bach unknown driver parser Exception
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class UnknownDriverParserException extends \InvalidArgumentException 
    implements ExceptionInterface
{
}
