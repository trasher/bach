<?php

/*
 * This file is part of the bach project.
*/

namespace Bach\IndexationBundle\Entity;

use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Service\DataBagFactory;

/**
* PreProcessor interface
*
* @author Anaphore PI Team
*/
abstract class PreProcessor
{
	protected $dataBagFactory;
	
	public function __construct(DataBagFactory $factory)
	{
		$this->dataBagFactory = $factory;
	}
	
	/**
	* Process the input file
	* @param DataBag $fileBag The file bag
	* @param SplFileInfo $fileProcessorInfo The file processor
	*/
	abstract public function process(DataBag $fileBag, \SplFileInfo $fileProcessorInfo);
}