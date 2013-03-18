<?php

namespace Anph\IndexationBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Anph\IndexationBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Anph\IndexationBundle\Entity\ArchFileIntegrationTask;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$document = new Document();
	    $form = $this->getDocumentForm($document);
	        $em2 = $this->getDoctrine()->getManager();
	    	
	    	$repository = $em2
	    	->getRepository('AnphIndexationBundle:ArchFileIntegrationTask');
	    	 
	    	$entities = $repository
	    	->createQueryBuilder('t')
	    	->orderBy('t.taskId', 'DESC')
	    	->getQuery()
	    	->getResult();
	    	$tasks = array();
	    	 
	    	foreach ($entities as $entity) {
	    		$spl = new \SplFileInfo($entity->getPath());
	    		$tasks[] = array(	'filename'	=>	$entity->getFilename(),
				    				'format'	=>	$entity->getFormat(),
				    				'size'		=>	$spl->getSize());
	    	
	    		switch ((int)$entity->getStatus()) {
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
	
		return $this->render('AnphIndexationBundle:Indexation:index.html.twig',array('tasks'=>$tasks,'form' => $form->createView()));
    }
    

    public function indexProcessAction()
    {  	
    	$document = new Document();
  		$form = $this->getDocumentForm($document);

    	if ($this->getRequest()->isMethod('POST')) {
    		$form->bind($this->getRequest());
    		if ($form->isValid()) {

    			$em = $this->getDoctrine()->getManager();
    	
    	
    			$em->persist($document);
    			$em->flush();
    			$task = new ArchFileIntegrationTask($document->getName(), realpath($document->getAbsolutePath()), $document->getExtension());
    			$em->persist($task);

    			$em->flush();
    		}else{
    			
    		}		
    	}
    	
    	return new RedirectResponse($this->get("router")->generate("anph_indexation_homepage"));
    }
    
    public function purgeAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('SELECT t FROM AnphIndexationBundle:ArchFileIntegrationTask t WHERE t.status > 0');
    	$tasks = $query->getResult();

    	foreach ($tasks as $task) {
    		$em->remove($task);
    	}
    	$em->flush();
    	return new RedirectResponse($this->get("router")->generate("anph_indexation_homepage"));
    }
    
    private function getDocumentForm($document)
    {
		$form = $this	->createFormBuilder($document)
    					->add('file','file',array("label" => "Fichier à indexer"))
    					->add('extension','choice',array(	"choices"	=>	array(	"ead"	=>	"EAD",
    																				"unimarc"	=>	"UNIMARC"),
    														"label"	=>	"Format du fichier"))
    					->getForm();    	

    	return $form;
    }
}