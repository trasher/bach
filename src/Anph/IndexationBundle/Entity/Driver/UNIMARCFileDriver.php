<?php

namespace Anph\IndexationBundle\Entity\Driver;

use Anph\IndexationBundle\FileDriverInterface;
use Symfony\Component\Finder\SplFileInfo;
use Anph\IndexationBundle\Entity\Unimarc\Parser;

class UNIMARCFileDriver implements FileDriverInterface
{
	public function process(SplFileInfo $fileInfo){
		new Parser($fileInfo);
	}
	
	public function getFileFormatName(){
		return "c01";
	}
}
