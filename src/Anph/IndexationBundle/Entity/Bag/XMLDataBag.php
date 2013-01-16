<?php

namespace Anph\IndexationBundle\Entity\Bag;

use Anph\IndexationBundle\Entity\DataBag;

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