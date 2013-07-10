<?php 

namespace Bach\IndexationBundle\Entity\PreProcessor;

use Bach\IndexationBundle\Entity\PreProcessor;
use Bach\IndexationBundle\Entity\DataBag;

class XSLTPreProcessor extends PreProcessor
{
	public function process(DataBag $fileBag, \SplFileInfo $fileProcessorInfo)
	{
		if($fileBag->getData() instanceof \DOMDocument)
		{
			try{
				$domXSL = new \DOMDocument();
				$xsl = new \XSLTProcessor();
				
				$domXSL->load($fileProcessorInfo->getRealPath());
				$xsl->importStyleSheet($domXSL);
				
				$fileBag->setData($xsl->transformToDoc($fileBag->getData()));
			} catch (\Exception $e) {
                //FIXME: at least, log something!
				return $fileBag;
			}
		}
		
		return $fileBag;
	}
}
?>