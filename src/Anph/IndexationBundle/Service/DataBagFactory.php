<?php

/*
* This file is part of the Bach project.
*/

namespace Anph\IndexationBundle\Service;

use Anph\IndexationBundle\Entity\DataBag;
use Anph\IndexationBundle\Entity\Bag\TextDataBag;
use Anph\IndexationBundle\Entity\Bag\XMLDataBag;

/**
* DataBagFactory put an input file in an appropriate DataBag
*/
class DataBagFactory
{
    public function encapsulate(\SplFileInfo $fileInfo)
    {
		switch ($fileInfo->getExtension()) {
			
			default:
			case 'txt':
				return new TextDataBag($fileInfo);
				break;
			
			case 'xml':
				return new XMLDataBag($fileInfo);
				break;
		}
    }
}