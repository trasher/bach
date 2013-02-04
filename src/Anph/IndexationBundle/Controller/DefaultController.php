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
	    		$spl = new \SplFileInfo($entity->getFilename());
	    		$tasks[] = array(	'filename'	=>	$spl->getBasename(),
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
    			$task = new ArchFileIntegrationTask(realpath($document->getAbsolutePath()), $document->getExtension());
    			$em->persist($task);

    			$em->flush();
    			    	
    			//$format = $document->getExtension();
    			$tasks = null;
    			 return new RedirectResponse($this->get("router")->generate("anph_indexation_homepage"));
		//return $this->render('AnphIndexationBundle:Indexation:index.html.twig',array('tasks'=>$tasks,'form' => $form->createView()));
    			
    	
    		}
    	}
    }
    
    private function getDocumentForm($document)
    {
		$form = $this->createFormBuilder($document)
    	// ->add('name')
    	->add('file','file',array(
    			"label" => "Fichiers : "))
    	->add('extension','choice',array("choices"	=>	array("ead"	=>	"EAD","unimarc"	=>	"UNIMARC"),
    									"label"	=>	"Format"))
    			->getForm()
    			;
    	
    	return $form;
    }
}