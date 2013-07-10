<?php

/*
 * This file is part of the bach project.
 */

namespace Bach\IndexationBundle;
/**
* DriverManager convert an input file into a UniversalFileFormat object
*
* @author Anaphore PI Team
*/
interface DriverMapperInterface
{
	/**
	* Translate the input data
	* @param array $data The input data
	* @return array Translated data
	*/
    public function translate($data);
}
