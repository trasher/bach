<?php

/*
 * This file is part of the bach project.
 */

namespace Anph\IndexationBundle;
/**
* DriverManager convert an input file into a UniversalFileFormat object
*
* @author Anaphore PI Team
*/
interface DriverMapperInterface
{
    public function translate($data);
}
