<?php

namespace Anph\IndexationBundle\Controller;
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
	    $form = $this->createFormBuilder($document)
	       // ->add('name')
	        ->add('file','file',array(
            "label" => "Fichiers : "))
	        ->getForm()
	    ;
	    
	
	    if ($this->getRequest()->isMethod('POST')) {
	        $form->bind($this->getRequest());
	        if ($form->isValid()) {
	        	
		        	$path = $document->getPath();
		        	$name = $document->getName();
		        	
		            $em = $this->getDoctrine()->getManager();
	
		
		            $em->persist($document);
		            $em->flush();
	
		            $em2 = $this->getDoctrine()->getManager();
		            
		            $format = $document->getExtension();
		             
		            $task = new ArchFileIntegrationTask($document->getAbsolutePath(), $format);
		            $em2->persist($task);
		            $em2->flush();
		
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
	    }
	    else{
	    	$name = null;
	    	$format = null;
	    	$em2 = $this->getDoctrine()->getManager();
	    	 
	    	
	    	$task = new ArchFileIntegrationTask($name, $format);
	    	$em2->persist($task);
	    	
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
    }
}