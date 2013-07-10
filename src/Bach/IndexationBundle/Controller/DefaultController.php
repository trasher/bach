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
     * Displays indexation queue and form
     *
     * @return void
     */
    public function indexAction()
    {
        $document = new Document();
        $form = $this->_getDocumentForm($document);
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
            case 0:
                $status = "";
                break;
            case 1:
                $status = "success";
                break;
            case 2:
            case 3:
                $status = "error";
                break;
            case 4:
                $status = "warning";
                break;
            }
            $tasks[count($tasks)-1]['status'] = $status;
        }

        return $this->render(
            'BachIndexationBundle:Indexation:index.html.twig',
            array(
                'tasks' =>$tasks,
                'form'  => $form->createView()
            )
        );
    }

    /**
     * Proceed indexation
     *
     * @return void
     */
    public function indexProcessAction()
    {
        $document = new Document();
        $form = $this->_getDocumentForm($document);

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                //store document reference
                $em = $this->getDoctrine()->getManager();
                $em->persist($document);
                $em->flush();

                //create a new task
                $task = new ArchFileIntegrationTask(
                    $document->getName(),
                    realpath($document->getAbsolutePath()),
                    $document->getExtension()
                );

                if ( $form->get('perform')->isClicked() ) {
                    //store file task in the database if perform action was requested
                    $em->persist($task);
                    $em->flush();
                } else {
                    //if performall action was requested, do not store in db
                    //and launch indexation process
                    $integrationService = $this->container
                        ->get('bach.indexation.process.arch_file_integration');
                    $res = $integrationService->integrate($task);
                }
            }
        }

        return new RedirectResponse(
            $this->get("router")->generate("bach_indexation_homepage")
        );
    }

    /**
     * Purge controller
     *
     * @return void
     */
    public function purgeAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery(
            'SELECT t FROM BachIndexationBundle:ArchFileIntegrationTask t ' .
            'WHERE t.status > 0'
        );
        $tasks = $query->getResult();

        foreach ($tasks as $task) {
            $em->remove($task);
        }
        $em->flush();
        return new RedirectResponse(
            $this->get("router")->generate("bach_indexation_homepage")
        );
    }

    /**
     * Get document form controller
     *
     * @param mixed $document Document
     *
     * @return void
     */
    private function _getDocumentForm($document)
    {
        $form = $this
            ->createFormBuilder($document)
            ->add(
                'file',
                'file',
                array(
                    "label" => "Fichier à indexer"
                )
            )
            ->add(
                'extension',
                'choice',
                array(
                    "choices" => array(
                        "ead"       => "EAD",
                        "unimarc"   => "UNIMARC"
                    ),
                    "label"    =>    "Format du fichier"
                )
            )
            ->add(
                'perform',
                'submit',
                array(
                    'label' => "Ajouter le fichier à la file d'attente",
                    'attr'  => array(
                        'class' => 'btn btn-primary'
                    )
                )
            )
            ->add(
                'performall',
                'submit',
                array(
                    'label' => "Lancer l'indexation",
                    'attr'  => array(
                        'class' => 'btn btn-primary'
                    )
                )
            )
            ->getForm();

        return $form;
    }
}
