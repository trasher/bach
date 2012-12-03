<?php

namespace Anph\IndexationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;
use Anph\IndexationBundle\Entity\Bag\XMLDataBag;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$manager = $this->container->get('anph_indexation.file_driver_manager');
    	$factory = $this->container->get('anph_indexation.data_bag_factory'); // Fourni le bon DataBag pour le fichier à indexer
    
    	//$fileInfo = new \SplFileInfo(__DIR__.'/FRAD027_PC.xml');
    	$fileInfo = new \SplFileInfo(__DIR__.'/FRAD027_404142R.xml');
    	//$fileInfo = new \SplFileInfo(__DIR__.'/fsor2709.c01');
    	
    	try{
    		$universalFileFormat = $manager->convert($factory->encapsulate($fileInfo),'ead');
    	}catch(BadInputFileFormatException $e){
    		echo $e->getMessage();
    	}
    
		return $this->render('AnphIndexationBundle:Indexation:index.html.twig');
    }
}
