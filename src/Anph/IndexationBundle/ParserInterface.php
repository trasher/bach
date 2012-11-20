<?php

/*
* This file is part of the bach project.
*/

namespace Anph\IndexationBundle;

use Anph\IndexationBundle\Entity\DataBag;

/**
* Parser interface
*
* @author Anaphore PI Team
*/
interface ParserInterface
{
	/**
	* The constructor
	* @param DataBag $bag The bag of data
	* @param array $configuration The caller driver configuration
	*/
	public function __construct(DataBag $bag, $configuration);
	
	
	/**
	* Parse the input data
	* @param DataBag $bag The bag of data
	*/
	public function parse(DataBag $bag);
	
	/**
	* Return the parser's ObjectTree
	* @return ObjectTree The parser's tree
	*/
    public function getTree();
}
