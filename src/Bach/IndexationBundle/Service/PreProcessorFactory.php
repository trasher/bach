<?php

/**
 * Pre processor factory
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Service;

use Bach\IndexationBundle\Entity\PreProcessor\XSLTPreProcessor;
use Bach\IndexationBundle\Entity\PreProcessor\JavaPreProcessor;
use Bach\IndexationBundle\Entity\PreProcessor\PHPPreProcessor;
use Bach\IndexationBundle\Entity\DataBag;


/**
 * Pre processor factory
 *
 * @category Indexation
 * @package  Bach
 * @author   Anaphore PI Team <unknown@unknown.com>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class PreProcessorFactory
{
    private $_dataBagFactory;

    /**
     * Constructor
     *
     * @param DataBagFactory $factory Data bag factory
     */
    public function __construct(DataBagFactory $factory)
    {
        $this->_dataBagFactory = $factory;
    }

    /**
     * Pre process data
     *
     * @param DataBag $fileBag           Data bag to process
     * @param string  $processorFilename Processor name
     *
     * @return mixed
     */
    public function preProcess(DataBag $fileBag, $processorFilename)
    {
        $spl = new \SplFileInfo($processorFilename);
        if ($spl->isFile()) {
            switch($spl->getExtension())
            {
            case 'xsl':
                $processor = new XSLTPreProcessor($this->_dataBagFactory);
                break;
            case 'java':
                $processor = new JavaPreProcessor($this->_dataBagFactory);
                break;
            case 'php':
                $processor = new PHPPreProcessor($this->_dataBagFactory);
                break;
            }

            if ( !is_null($processor) ) {
                return $processor->process($fileBag, $spl);
            } else {
                return $fileBag;
            }
        } else {
            return $fileBag;
        }
    }
}
