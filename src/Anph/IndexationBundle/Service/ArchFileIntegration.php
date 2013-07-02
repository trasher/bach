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

namespace Anph\IndexationBundle\Service;

use Anph\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;
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
     * Intagrate files in queue into the database
     *
     * @return void
     */
    public function integrate()
    {
        $repository = $this->_entityManager
            ->getRepository('AnphIndexationBundle:ArchFileIntegrationTask');

        $tasks = $repository->findByStatus(0);

        foreach ($tasks as $task) {
            $spl = new \SplFileInfo($task->getPath());
            $format = $task->getFormat();
            $preprocessor = $task->getPreprocessor();

            try{
                $universalFileFormats = $this->_manager->convert(
                    $this->_factory->encapsulate($spl),
                    $format,
                    $preprocessor
                );

                foreach ($universalFileFormats as $universalFileFormat) {
                    $this->_entityManager->persist($universalFileFormat);
                }

                $task->setStatus(1);
                //$this->_entityManager->remove($task);
                $this->_entityManager->persist($task);
                $this->_entityManager->flush();

                $sca = new SolrCoreAdmin();
                $coreNames = $sca->getStatus()->getCoreNames();
                foreach ( $coreNames as $core ) {
                    $sca->fullImport($core);
                }
            }catch(BadInputFileFormatException $e){
                $task->setStatus(2);
                $this->_entityManager->persist($task);
                $this->_entityManager->flush();
            }catch(UnknownDriverParserException $e){
                $task->setStatus(3);
                $this->_entityManager->persist($task);
                $this->_entityManager->flush();
            }catch(\DomainException $e){
                $task->setStatus(4);
                $this->_entityManager->persist($task);
                $this->_entityManager->flush();
            }
        }
    }
}
