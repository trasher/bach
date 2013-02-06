<?php

namespace Anph\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
    	$client = $this->get('solarium.client');
    	$query = $client->createSelect();
    	
        return $this->render('AnphHomeBundle:Default:index.html.twig');
    }
}
