<?php

/*
* This file is part of the Bach project.
*/

namespace Bach\IndexationBundle\Service;

use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Entity\Bag\TextDataBag;
use Bach\IndexationBundle\Entity\Bag\XMLDataBag;

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