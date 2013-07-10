<?php

namespace Bach\IndexationBundle\Entity\Bag;

use Bach\IndexationBundle\Entity\DataBag;

class XMLDataBag extends DataBag
{
	/**
	* The constructor
	* @param SplFileInfo $fileInfo The input file
	*/
	public function __construct(\SplFileInfo $fileInfo)
	{
		$this->type = "xml";
		$this->fileInfo = $fileInfo;
		$dom = new \DOMDocument();
		$dom->load($fileInfo->getRealPath());
		$this->data = $dom;
	}
}