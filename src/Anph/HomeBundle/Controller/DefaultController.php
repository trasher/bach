<?php

namespace Anph\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AnphHomeBundle:Default:index.html.twig');
    }
}
