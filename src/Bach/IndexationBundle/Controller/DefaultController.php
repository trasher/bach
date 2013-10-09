<?php
/**
 * Default indexation controller
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\IndexationBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Bach\IndexationBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Bach\IndexationBundle\Entity\ArchFileIntegrationTask;
use Bach\IndexationBundle\Form\Type\DocumentType;

/**
 * Default indexation controller
 *
 * PHP version 5
 *
 * @category Indexation
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DefaultController extends Controller
{

    /**
     * Displays current indexed documents
     *
     * @return void
     */
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository('BachIndexationBundle:Document');
        $documents = $repo->getPublishedDocuments();

        return $this->render(
            'BachIndexationBundle:Indexation:index.html.twig',
            array(
                'documents'     => $documents
            )
        );
    }

    /**
     * Add new documents to queue or index
     *
     * @return void
     */
    public function addAction()
    {
        $document = new Document();
        $form = $this->createForm('document', $document);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $document = $form->getData();
            //set core name
            $document->setCorename(
                $this->container->getParameter(
                    $document->getExtension() . '_corename'
                )
            );
            //generate document id
            $document->generateDocId();

            $em = $this->getDoctrine()->getManager();

            //check if document already exists, and remove it first
            //TODO: parametize or find a better solution.
            //Catching exception in flush does not work because EntityManager
            //is dead after the Exception occurs :(
            $qb = $em->createQueryBuilder();
            $qb->add('select', 'd')
                ->add('from', 'BachIndexationBundle:Document d')
                ->add('where', 'd.docid = :id')
                ->setParameter('id', $document->getDocId());

            $query = $qb->getQuery();
            $existing = $query->getResult();

            if ( count($existing) > 0 ) {
                $existing_doc = $existing[0];
                $existing_doc->setFile($document->getFile());
                $document = $existing_doc;
            } else {
                //store document reference
                $em->persist($document);
                $em->flush();
            }

            //create a new task
            $task = new ArchFileIntegrationTask($document);

            if ( $form->get('perform')->isClicked() ) {
                //store file task in the database if perform action was requested
                $em->persist($task);
                $em->flush();
                return new RedirectResponse(
                    $this->get("router")->generate("bach_indexation_queue")
                );
            } else {
                //if performall action was requested, do not store task in db
                //and launch indexation process

                if ( count($existing) > 0 ) {
                    //first, remove document if it already has been published
                    $this->removeDocumentsAction(
                        array(
                            $existing[0]->getExtension() . '::' .
                            $existing[0]->getId()
                        )
                    );
                    //persist document *after* deletion, to upload new file and so on
                    //we have to change a field know to database, for doctrine to process persistence
                    $task->getDocument()->setName(null);
                    $em->persist($task->getDocument());
                    $em->flush();
                }

                $integrationService = $this->container
                    ->get('bach.indexation.process.arch_file_integration');
                $res = $integrationService->integrate($task);

                return new RedirectResponse(
                    $this->get("router")->generate("bach_indexation_homepage")
                );
            }
        }

        $tf = $this->container->get('bach.indexation.typesfiles');

        return $this->render(
            'BachIndexationBundle:Indexation:add.html.twig',
            array(
                'directory_contents'    => null,
                'upload_form'           => $form->createView(),
                'existing_files'        => $tf->getExistingFiles()
            )
        );
    }

    /**
     * Displays indexation queue and form
     *
     * @return void
     */
    public function queueAction()
    {
        $em2 = $this->getDoctrine()->getManager();

        $repository = $em2
            ->getRepository('BachIndexationBundle:ArchFileIntegrationTask');

        $entities = $repository
            ->createQueryBuilder('t')
            ->orderBy('t.taskId', 'DESC')
            ->getQuery()
            ->getResult();
        $tasks = array();

        foreach ($entities as $entity) {
            $spl = new \SplFileInfo($entity->getPath());
            $tasks[] = array(
                'filename'  => $entity->getFilename(),
                'format'    => $entity->getFormat(),
                'size'      => $spl->getSize()
            );

            switch ( (int)$entity->getStatus() ) {
            default:
            case ArchFileIntegrationTask::STATUS_NONE:
                $status = "";
                break;
            case ArchFileIntegrationTask::STATUS_OK:
                $status = "success";
                break;
            case ArchFileIntegrationTask::STATUS_KO:
            default:
                $status = "error";
                break;
            }
            $tasks[count($tasks)-1]['status'] = $status;
        }

        return $this->render(
            'BachIndexationBundle:Indexation:queue.html.twig',
            array(
                'tasks'         => $tasks
            )
        );
    }

    /**
     * Purge controller
     *
     * @return void
     */
    public function purgeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT t FROM BachIndexationBundle:ArchFileIntegrationTask t ' .
            'WHERE t.status > ' . ArchFileIntegrationTask::STATUS_NONE
        );
        $tasks = $query->getResult();

        foreach ($tasks as $task) {
            $em->remove($task);
        }
        $em->flush();
        return new RedirectResponse(
            $this->get("router")->generate("bach_indexation_queue")
        );
    }

    /**
     * Remove selected indexed documents, in both database and Solr
     *
     * @param array $documents List of id to remove. If missing,
     *                         we'll take documents in GET.
     *
     * @return void
     */
    public function removeDocumentsAction($documents = null)
    {
        if ( $documents === null ) {
            $documents = $this->get('request')->request->get('documents');
        }

        $extensions = array();
        $ids = array();
        foreach ( $documents as $document) {
            list($extension, $id) = explode('::', $document);
            if ( !isset($extensions[$extension]) ) {
                $extensions[$extension] = array();
            }
            $extensions[$extension][] = $id;
            $ids[] = $id;
        }

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->add('select', 'd')
            ->add('from', 'BachIndexationBundle:Document d')
            ->add('where', 'd.id IN (:ids)')
            ->setParameter('ids', $ids);

        $query = $qb->getQuery();
        $docs = $query->getResult();
        foreach ($docs as $doc) {
            $em->remove($doc);
        }

        //remove solr indexed documents
        $client = $this->get("solarium.client");
        $update = $client->createUpdate();

        //remove documents contents
        foreach ( $extensions as $extension=>$ids ) {
            $ent = '';
            switch ( $extension ) {
            case 'ead':
                $ent = 'BachIndexationBundle:EADFileFormat';
                break;
            default:
                throw new \RuntimeException('Unknown extension ' . $extension);
                break;
            }

            $qb = $em->createQueryBuilder();
            $qb->add('select', 'e')
                ->add('from', $ent . ' e')
                ->add('where', 'e.doc_id IN (:ids)')
                ->setParameter('ids', $ids);

            $query = $qb->getQuery();
            $contents = $query->getResult();
            foreach ($contents as $content) {
                $em->remove($content);
                //FIXME: it would be more efficient to remove all indexes based
                //on document unique identifier
                $update->addDeleteQuery('uniqid:' . $content->getUniqid());
            }
        }

        $em->flush();

        $update->addCommit();
        $result = $client->update($update);

        return new RedirectResponse(
            $this->get("router")->generate("bach_indexation_homepage")
        );
    }

    /**
     * Remove all indexed documents, in both database and Solr
     *
     * @return void
     */
    public function emptyAction()
    {
        //remove solr indexed documents
        //FIXME: check if solr cores exists!
        $client = $this->get("solarium.client.ead");
        $update = $client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $result = $client->update($update);

        $client = $this->get("solarium.client.unimarc");
        $update = $client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $result = $client->update($update);

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        } catch (\Exception $e) {
            //database does not support that. it is ok.
        }

        $connection->executeUpdate(
            $platform->getTruncateTableSQL('UniversalFileFormat', true)
        );

        $connection->executeUpdate(
            $platform->getTruncateTableSQL('EADDates', true)
        );
        $connection->executeUpdate(
            $platform->getTruncateTableSQL('EADIndexes', true)
        );
        $connection->executeUpdate(
            $platform->getTruncateTableSQL('EADDaos', true)
        );
        $connection->executeUpdate(
            $platform->getTruncateTableSQL('EADUniversalFileFormat', true)
        );

        //FIXME: remove integration task as well?
        //FIXME: are files not deleted this way?
        $connection->executeUpdate(
            $platform->getTruncateTableSQL('Document', true)
        );

        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            //database does not support that. it is ok.
        }

        return new RedirectResponse(
            $this->get("router")->generate("bach_indexation_homepage")
        );
    }
}
