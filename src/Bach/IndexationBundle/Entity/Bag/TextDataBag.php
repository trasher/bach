<?php

namespace Bach\IndexationBundle\Entity\Bag;

use Bach\IndexationBundle\Entity\DataBag;

class TextDataBag extends DataBag
{
	/**
	* The constructor
	* @param SplFileInfo $fileInfo The input file
	*/
	public function __construct(\SplFileInfo $fileInfo)
	{
		$this->type = "txt";
		$this->fileInfo = $fileInfo;
		$this->data = file_get_contents($fileInfo->getRealPath());
	}
}