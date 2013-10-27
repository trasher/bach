<?php
/**
 * Bach XSLT preprocessor
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity\PreProcessor;

use Bach\IndexationBundle\Entity\PreProcessor;
use Bach\IndexationBundle\Entity\DataBag;

/**
 * Bach XSLT preprocessor
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class XSLTPreProcessor extends PreProcessor
{

    /**
     * Process
     *
     * @param DataBag     $fileBag           Databag
     * @param SplFileInfo $fileProcessorInfo File processor info
     *
     * @return DataBag
     */
    public function process(DataBag $fileBag, \SplFileInfo $fileProcessorInfo)
    {
        if ($fileBag->getData() instanceof \DOMDocument) {
            try{
                $domXSL = new \DOMDocument();
                $xsl = new \XSLTProcessor();

                $domXSL->load($fileProcessorInfo->getRealPath());
                $xsl->importStyleSheet($domXSL);

                $fileBag->setData($xsl->transformToDoc($fileBag->getData()));
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $fileBag;
    }
}
?>
