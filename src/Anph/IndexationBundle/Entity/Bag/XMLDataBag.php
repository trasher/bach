<?php

namespace Anph\IndexationBundle\Entity\Bag;

use Symfony\Component\Finder\SplFileInfo;
use Anph\IndexationBundle\Entity\DataBag;

class XMLDataBag extends DataBag
{
	/**
	* The constructor
	* @param SplFileInfo $fileInfo The input file
	*/
	public function __construct(SplFileInfo $fileInfo)
	{
		$this->type = "xml";
		$dom = new \DOMDocument();
		$dom->load($fileInfo->getRelativePath());
		$this->data = $dom;
	}
}