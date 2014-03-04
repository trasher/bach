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
use Bach\IndexationBundle\Entity\Document;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Bach\IndexationBundle\Entity\ArchFileIntegrationTask;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;

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
     * @param int $page Current page
     *
     * @return void
     */
    public function indexAction($page = 1)
    {
        $show = 30;
        $repo = $this->getDoctrine()->getRepository('BachIndexationBundle:Document');
        $documents = $repo->getPublishedDocuments($page, $show);

        return $this->render(
            'BachIndexationBundle:Indexation:index.html.twig',
            array(
                'documents'     => $documents,
                'currentPage'   => $page,
                'lastPage'      => ceil(count($documents) / $show)
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
        $document = new Document(true);
        $document->setUploadDir(
            $this->container->getParameter('upload_dir')
        );
        $form = $this->createForm('document', $document);
        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            // enable memory profiling
            if (extension_loaded('memprof')) {
                memprof_enable();
            }

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
                $document->setUploadDir(
                    $this->container->getParameter('upload_dir')
                );
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
                    //we have to change a field know to database,
                    //for doctrine to process persistence
                    $task->getDocument()->setName(null);
                    $em->persist($task->getDocument());
                    $em->flush();
                }

                $integrationService = $this->container
                    ->get('bach.indexation.process.arch_file_integration');
                $res = $integrationService->integrate($task);

                $configreader = $this->container
                    ->get('bach.administration.configreader');
                $sca = new SolrCoreAdmin($configreader);
                $sca->fullImport($task->getDocument()->getCorename());

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
            $entity->getDocument()->setUploadDir(
                $this->container->getParameter('upload_dir')
            );
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

        //remove solr indexed documents per core
        $updates = array();
        $clients = array();

        foreach ($docs as $doc) {
            if ( !isset($updates[$doc->getCorename()]) ) {
                $client = $this->get('solarium.client.' . $doc->getExtension());
                $clients[$doc->getCorename()] = $client;
                $updates[$doc->getCorename()] = $client->createUpdate();
            }
            $doc->setUploadDir($this->container->getParameter('upload_dir'));
            $update = $updates[$doc->getCorename()];
            if ( $doc->getExtension() === 'matricules' ) {
                $update->addDeleteQuery('headerId:' . $doc->getId());
            } else {
                $update->addDeleteQuery('headerId:' . $doc->getDocId());
            }
            $em->remove($doc);
        }

        foreach ( $updates as $key=>$update ) {
            $client = $clients[$key];
            $update->addCommit(null, null, true);
            $result = $client->update($update);
        }

        $em->flush();

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

        /*$client = $this->get("solarium.client.unimarc");
        $update = $client->createUpdate();
        $update->addDeleteQuery('*:*');
        $update->addCommit();
        $result = $client->update($update);*/

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
            $platform->getTruncateTableSQL('EADParentTitle', true)
        );
        $connection->executeUpdate(
            $platform->getTruncateTableSQL('EADUniversalFileFormat', true)
        );

        //FIXME: remove integration task as well?
        //FIXME: are files not deleted this way?
        $connection->executeUpdate(
            $platform->getTruncateTableSQL('Document', true)
        );

        $connection->executeUpdate(
            $platform->getTruncateTableSQL('ArchFileIntegrationTask', true)
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

    /**
     * Validate document
     *
     * @param int     $docid Document unique identifier
     * @param string  $type  Document type
     * @param boolean $ajax  Called from ajax
     *
     * @return void
     */
    public function validateDocumentAction($docid, $type, $ajax = false)
    {
        $msg = '';
        //for now, we can only validate EAD DTD
        if ( $type !== 'ead' ) {
             $msg = _('Could not validate non EAD documents (for now).');
        } else {
            $repo = $this->getDoctrine()
                ->getRepository('BachIndexationBundle:Document');
            $document = $repo->findOneByDocid($docid);

            if ( $document->isUploaded() ) {
                $document->setUploadDir(
                    $this->container->getParameter('upload_dir')
                );
            } else {
                $document->setStoreDir(
                    $this->container->getParameter('bach.typespaths')[$type]
                );
            }
            $xml_file = $document->getAbsolutePath();

            if ( !file_exists($xml_file) ) {
                $msg = str_replace(
                    '%docid%',
                    $docid,
                    _('Corresponding file for %docid% document no longer exists on disk.')
                );
            } else {
                $oxml_document = new \DOMDocument();
                $oxml_document->load($xml_file);

                $root = 'ead';
                $creator = new \DOMImplementation;
                $doctype = $creator->createDocumentType(
                    $root,
                    null,
                    __DIR__ . '/../Resources/dtd/ead-2002/ead.dtd'
                );
                $xml_document = $creator->createDocument(null, null, $doctype);
                $xml_document->encoding = "utf-8";

                $oldNode = $oxml_document->getElementsByTagName($root)->item(0);
                $newNode = $xml_document->importNode($oldNode, true);
                $xml_document->appendChild($newNode);

                libxml_use_internal_errors(true);

                $valid = @$xml_document->validate();

                if ( $valid ) {
                    $msg = str_replace(
                        '%docid%',
                        $docid,
                        _('Document %docid% is valid and DTD compliant!')
                    );
                } else {
                    $msg = str_replace(
                        array('%type%', '%docid%'),
                        array($type, $docid),
                        _('%type% document %docid% is not valid!')
                    );
                }

                foreach ( libxml_get_errors() as $error ) {
                    $xml_errors[] = $error->message;
                    $this->get('session')->getFlashBag()->add(
                        'documentvalidation_errors',
                        $error->message . ' (line: ' . $error->line .
                        ' col: ' . $error->column . ')'
                    );
                }
            }
        }

        $this->get('session')->getFlashBag()->add(
            'documentvalidation',
            $msg
        );

        if ( $ajax === false ) {
            return new RedirectResponse(
                $this->get("router")->generate("bach_indexation_homepage")
            );
        }

    }
}
