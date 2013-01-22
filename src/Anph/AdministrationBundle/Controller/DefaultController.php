<?php

namespace Anph\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Anph\AdministrationBundle\Entity\SolrShema\XMLProcess;

//use Anph\AdministrationBundle\Controller\XMLProcess;

class DefaultController extends Controller
{
	public function indexAction()
	{
		//$process = new XMLProcess(__DIR__.'/../Resources/config/schema.xml');
		
		//$process->importXML();
		//$this->get("anph.administration.xmlimport")->importXML(__DIR__.'/../Resources/config/schema.xml');
		return $this->render('AdministrationBundle:Default:index.html.twig');
	}



	
}
