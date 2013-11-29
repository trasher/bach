<?php
/**
 * Bach Java preprocessor
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
use Symfony\Component\Process\Process;

/**
 * Bach Java preprocessor
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class JavaPreProcessor extends PreProcessor
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
        $process = new Process("java ".$fileProcessorInfo->getRealPath());
        $process->setStdin($fileBag->getFileInfo()->getRealPath());
        $process->run();

        if ($process->isSuccessful()) {
            return $this->dataBagFactory->encapsulate(
                $fileBag->getFileInfo()->getRealPath()
            );
        } else {
            return $fileBag;
        }
    }
}

