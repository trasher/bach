<?php

/*
 * This file is part of the bach project.
 */

namespace Anph\IndexationBundle\Exception;

use Symfony\Component\Routing\Exception\ExceptionInterface;

/**
 * BadFileInputFormat
 *
 * @author Anpahore PI Team
 */
class UnknownDriverParserException extends \InvalidArgumentException implements ExceptionInterface
{
}
