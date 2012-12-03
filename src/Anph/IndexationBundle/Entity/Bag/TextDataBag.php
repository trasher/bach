<?php

namespace Anph\IndexationBundle\Entity\Bag;

use Anph\IndexationBundle\Entity\DataBag;

class TextDataBag extends DataBag
{
	/**
	* The constructor
	* @param SplFileInfo $fileInfo The input file
	*/
	public function __construct(\SplFileInfo $fileInfo)
	{
		$this->type = "txt";
		$this->data = file_get_contents($fileInfo->getRealPath());
	}
}