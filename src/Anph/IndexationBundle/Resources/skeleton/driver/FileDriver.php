<?php

namespace {{ namespace }};

use Anph\IndexationBundle\FileDriverInterface;
use Symfony\Component\Finder\SplFileInfo;

class {{ driver }} implements FileDriverInterface
{
	public function process(SplFileInfo $fileInfo){
		
	}
	
	public function getFileFormatName(){
		return "{{ format }}";
	}
}
