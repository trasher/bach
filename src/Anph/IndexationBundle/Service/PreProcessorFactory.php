<?php

/*
* This file is part of the Bach project.
*/

namespace Anph\IndexationBundle\Service;

use Anph\IndexationBundle\PreProcessorInterface;
use Anph\IndexationBundle\Entity\PreProcessor\XSLTPreProcessor;
use Anph\IndexationBundle\Entity\PreProcessor\JavaPreProcessor;
use Anph\IndexationBundle\Entity\PreProcessor\PHPPreProcessor;
use Anph\IndexationBundle\Entity\DataBag;

/**
* PreProcessorFactory provides PreProcessor
*
* @author Anaphore PI Team
*/
class PreProcessorFactory
{
	private $dataBagFactory;
	
	public function __construct(DataBagFactory $factory)
	{
		$this->dataBagFactory = $factory;
	}
	
	public function preProcess(DataBag $fileBag, $processorFilename)
	{
		$spl = new \SplFileInfo($processorFilename);
		if ($spl->isFile()) {
			switch($spl->getExtension())
			{
				case 'xsl':
					$processor = new XSLTPreProcessor($this->dataBagFactory);
					break;
					
				case 'java':
					$processor = new JavaPreProcessor($this->dataBagFactory);
					break;
					
				case 'php':
					$processor = new PHPPreProcessor($this->dataBagFactory);
					break;
			}
			
			if(!is_null($processor))
			{
				return $processor->process($fileBag, $spl);
			}else
			{
				return $fileBag;
			}
		} else {
			return $fileBag;
		}
	}
}