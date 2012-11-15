<?php

namespace Anph\AdministrationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AdministrationBundle:Default:index.html.twig', array('name' => $name));
    }
}
