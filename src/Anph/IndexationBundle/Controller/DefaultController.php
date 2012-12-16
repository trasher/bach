<?php

namespace Anph\IndexationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Anph\IndexationBundle\Entity\ArchFileIntegrationTask;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	/*
    	$manager = $this->container->get('anph.indexation.file_driver_manager');
    	$factory = $this->container->get('anph.indexation.data_bag_factory'); // Fourni le bon DataBag pour le fichier à indexer
    
    	//$fileInfo = new \SplFileInfo(__DIR__.'/FRAD027_PC.xml');
    	$fileInfo = new \SplFileInfo(__DIR__.'/../Resources/benchmark/data/EAD/6_FRAD027_404142R_220mo.xml');
    	//$fileInfo = new \SplFileInfo(__DIR__.'/fsor2709.c01');
    	
    	try{
    		$universalFileFormats = $manager->convert($factory->encapsulate($fileInfo),'ead');
		//	var_dump($universalFileFormats);
    	}catch(BadInputFileFormatException $e){
    		echo $e->getMessage();
    	}
    	echo memory_get_usage() . "\n"; // 36640
    	   /**/
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$file = __DIR__.'/FRAD027_404142R.xml';
    	$format = 'unimarc';
    	
    	
    	$task = new ArchFileIntegrationTask($file, $format);
    	$em->persist($task);
    	//$em->flush();
    	
    	$repository = $em
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
    	
		return $this->render('AnphIndexationBundle:Indexation:index.html.twig',array('tasks'=>$tasks));
    }
}
