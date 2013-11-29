<?php
/**
 * Bach indexation pre processor interface
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
/*
 * This file is part of the bach project.
*/

namespace Bach\IndexationBundle\Entity;

use Bach\IndexationBundle\Entity\DataBag;
use Bach\IndexationBundle\Service\DataBagFactory;


/**
 * Bach indexation pre processor interface
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
abstract class PreProcessor
{
    protected $dataBagFactory;

    /**
     * Constructor
     *
     * @param DataBagFactory $factory Data bag factory
     */
    public function __construct(DataBagFactory $factory)
    {
        $this->dataBagFactory = $factory;
    }

    /**
     * Process the input file
     *
     * @param DataBag     $fileBag           The file bag
     * @param SplFileInfo $fileProcessorInfo The file processor
     *
     * @return DataBag preprocessed
     */
    abstract public function process(DataBag $fileBag, \SplFileInfo $fileProcessorInfo);
}
