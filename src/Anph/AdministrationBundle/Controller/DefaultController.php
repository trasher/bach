<?php

namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

//use Anph\AdministrationBundle\Controller\XMLProcess;

class DefaultController extends Controller
{
	public function indexAction()
	{
		return $this->render('AdministrationBundle:Default:index.html.twig');
	}
	
	public function coreadminAction()
	{
		return $this->render('AdministrationBundle:Default:coreadmin.html.twig');
	}
	
	public function performanceAction()
	{
		return $this->render('AdministrationBundle:Default:performance.html.twig');
	}
	
	public function dashboardAction()
	{
	    $coreName = $this->getRequest()->request->get('selectedCore');
	    if (!isset($coreName)) {
	        $coreName = 'none';
	    }
        $session = $this->getRequest()->getSession();
        $session->set('coreName', $coreName);
        if ($coreName == 'none') {
            $session->set('xmlP', null);
        } else {
            $session->set('xmlP', new XMLProcess($coreName));
        }
        return $this->render('AdministrationBundle:Default:dashboard.html.twig', array('coreName' => $coreName));
	}
}
