<?php
/**
 * Archival file integration in database
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

use Doctrine\ORM\EntityManager;
use Bach\IndexationBundle\Entity\FileFormat;
use Bach\IndexationBundle\Entity\IntegrationTask;

/**
 * Archival file integration in database
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class ArchFileIntegration
{
    private $_manager;
    private $_factory;
    private $_entityManager;

    /**
     * Instanciate Service
     *
     * @param FileDriverManager $manager       The file driver manager
     * @param DataBagFactory    $factory       The databag factory instance
     * @param EntityManager     $entityManager The entity manager
     */
    public function __construct(FileDriverManager $manager,
        DataBagFactory $factory, EntityManager $entityManager
    ) {
        $this->_manager = $manager;
        $this->_factory = $factory;
        $this->_entityManager = $entityManager;
    }

    /**
     * Integrate files in queue into the database
     *
     * @return void
     */
    public function proceedQueue()
    {
        $repository = $this->_entityManager
            ->getRepository('BachIndexationBundle:IntegrationTask');
        $tasks = $repository->findByStatus(IntegrationTask::STATUS_NONE);

        foreach ($tasks as $task) {
            try {
                $this->integrate($task);
                $task->setStatus(IntegrationTask::STATUS_OK);
            } catch(\Exception $e) {
                $task->setStatus(IntegrationTask::STATUS_KO);
            }

            //anyways, presist task
            $this->_entityManager->persist($task);
            $this->_entityManager->flush();
        }

    }

    /**
     * Proceed task database integration
     *
     * @param IntegrationTask $task  Task to proceed
     * @param boolean                 $flush Wether to flush
     *
     * @return void
     */
    public function integrate(IntegrationTask $task, $flush = true)
    {
        $spl = new \SplFileInfo($task->getPath());
        $doc = $task->getDocument();
        $format = $task->getFormat();
        $preprocessor = $task->getPreprocessor();

        $this->_manager->convert(
            $this->_factory->encapsulate($spl),
            $format,
            $doc,
            $flush,
            $preprocessor
        );
    }

    /**
     * Integrate multiple tasks at once
     *
     * @param array       $tasks    Tasks to integrate
     * @param progressbar $progress Progress bar
     *
     * @return void
     */
    public function integrateAll($tasks, $progress)
    {
        $count = 0;
        $cleared = false;
        foreach ( $tasks as $task) {
            if ( $cleared ) {
                $doc = $task->getDocument();
                $doc = $this->_entityManager->merge($doc);
                $task->setDocument($doc);
            }
            $progress->advance();
            $this->integrate($task, false);
            $count++;

            if ( $count % 20000 === 0 ) {
                $this->_entityManager->flush();
                $this->_entityManager->clear();
                $cleared = true;
            }
        }

        $this->_entityManager->flush();
        $this->_entityManager->clear();

    }
}
