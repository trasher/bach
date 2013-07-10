<?php 

namespace Bach\IndexationBundle\Entity\PreProcessor;

use Bach\IndexationBundle\Entity\PreProcessor;
use Bach\IndexationBundle\Entity\DataBag;
use Symfony\Component\Process\PhpProcess;

class PHPPreProcessor extends PreProcessor
{
	public function process(DataBag $fileBag, \SplFileInfo $fileProcessorInfo)
	{
		$process = new PhpProcess(file_get_contents($fileProcessorInfo->getRealPath()));
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