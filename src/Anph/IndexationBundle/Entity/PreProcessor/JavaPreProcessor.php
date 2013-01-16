<?php 

namespace Anph\IndexationBundle\Entity\PreProcessor;

use Anph\IndexationBundle\Entity\PreProcessor;
use Anph\IndexationBundle\Entity\DataBag;
use Symfony\Component\Process\Process;

class JavaPreProcessor extends PreProcessor
{
	public function process(DataBag $fileBag, \SplFileInfo $fileProcessorInfo)
	{
		$process = new Process("java ".$fileProcessorInfo->getRealPath());
		$process->setStdin($fileBag->getFileInfo()->getRealPath());
		$process->run();
		
		if($process->isSuccessful())
		{
			return $this->dataBagFactory->encapsulate($fileBag->getFileInfo()->getRealPath());
		}
		else
		{
			return $fileBag;
		}
	}
}
?>