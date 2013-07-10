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

use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Bach\IndexationBundle\Exception\BadInputFileFormatException;
use Bach\IndexationBundle\Exception\UnknownDriverParserException;
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

        $tasks = $repository->findByStatus(0);

        foreach ($tasks as $task) {
            try{
                $this->integrate($task);
                $task->setStatus(1);
            }catch(BadInputFileFormatException $e){
                $task->setStatus(2);
            }catch(UnknownDriverParserException $e){
                $task->setStatus(3);
            }catch(\DomainException $e){
                $task->setStatus(4);
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
        $format = $task->getFormat();
        $preprocessor = $task->getPreprocessor();

        $universalFileFormats = $this->_manager->convert(
            $this->_factory->encapsulate($spl),
            $format,
            $preprocessor
        );

        foreach ($universalFileFormats as $universalFileFormat) {
            $this->_entityManager->persist($universalFileFormat);
        }

        $this->_entityManager->flush();

        $sca = new SolrCoreAdmin();
        $coreNames = $sca->getStatus()->getCoreNames();
        foreach ( $coreNames as $core ) {
            $sca->fullImport($core);
        }
    }
}
