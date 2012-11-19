<?php

namespace Anph\IndexationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\SplFileInfo;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$manager = $this->container->get('anph_indexation.file_driver_manager');
    	
    	//$fileInfo = new SplFileInfo("FRAD027_PC.xml",__DIR__.'/FRAD027_PC.xml',__DIR__);
    	//$fileInfo = new SplFileInfo("FRAD027_404142R.xml",__DIR__.'/FRAD027_404142R.xml',__DIR__);
    	$fileInfo = new SplFileInfo("fsor2709.c01",__DIR__.'/fsor2709.c01',__DIR__);
    	
    	try{
    		$universalFileFormat = $manager->convert($fileInfo);
    	}catch(BadInputFileFormatException $e){
    		echo $e->getMessage();
    	}
    	/*
    	$client = $this->get('solarium.client');
    	$select = $client->createSelect();
    	$select->setQuery('foo');
    	$results = $client->select($select);
    	var_dump($results->getNumFound());
    	*/
		return $this->render('AnphIndexationBundle:Indexation:index.html.twig');
    }
}
