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
            ->getRepository('BachIndexationBundle:ArchFileIntegrationTask');
        $tasks = $repository->findByStatus(ArchFileIntegrationTask::STATUS_NONE);

        foreach ($tasks as $task) {
            try {
                $this->integrate($task);
                $task->setStatus(ArchFileIntegrationTask::STATUS_OK);
            } catch(\Exception $e) {
                $task->setStatus(ArchFileIntegrationTask::STATUS_KO);
            }

            //anyways, presist task
            $this->_entityManager->persist($task);
            $this->_entityManager->flush();
        }

    }

    /**
     * Proceed task database integration
     *
     * @param Entity $task Task to proceed
     *
     * @return void
     */
    public function integrate($task)
    {
        $spl = new \SplFileInfo($task->getPath());
        $doc = $task->getDocument();
        $format = $task->getFormat();
        $preprocessor = $task->getPreprocessor();

        $universalFileFormats = $this->_manager->convert(
            $this->_factory->encapsulate($spl),
            $format,
            $preprocessor
        );

        $count = 0;
        //disable SQL Logger...
        $this->_entityManager->getConnection()->getConfiguration()
            ->setSQLLogger(null);
        foreach ($universalFileFormats as $universalFileFormat) {
            $universalFileFormat->setDocId($doc);
            $this->_entityManager->persist($universalFileFormat);
            unset($universalFileFormat);

            $count++;

            if ( $count % 100 === 0 ) {
                $this->_entityManager->flush();
            }
        }

        $this->_entityManager->flush();
        $this->_entityManager->clear();

        if ( function_exists('memprof_enable') ) {
            memprof_dump_callgrind(
                fopen(
                    '/var/www/bach/app/cache/integrate.callgrind.out',
                    'w'
                )
            );
        }
    }
}
