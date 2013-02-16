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
		//$process = new XMLProcess(__DIR__.'/../Resources/config/schema.xml');
		
		//$process->importXML();
		//$this->get("anph.administration.xmlimport")->importXML(__DIR__.'/../Resources/config/schema.xml');
		return $this->render('AdministrationBundle:Default:index.html.twig');
	}
	
	public function fieldstypeAction()
	{
		return $this->render('AdministrationBundle:Default:fieldstype.html.twig');
	}
	
	public function tockenizersAction()
	{
		return $this->render('AdministrationBundle:Default:tockenizers.html.twig');
	}
	
	public function analyzersAction(Request $request)
	{
		$defaultData = array('name' => 'bonjour');
		$form = $this->createFormBuilder($defaultData)
		->add('name', 'text')
		->add('email', 'text')
		->add('message', 'text')
		->add('fieldsType', 'choice', array(
				'choices'   => array('type 1' => 'Male', 'type 2' => 'Female'),
				'required'  => false,
		))
		->add('indexed', 'checkbox', array(
				'label'     => 'indexed',
				'required'  => false,
		))
		->add('stored', 'checkbox', array(
				'label'     => 'Stored',
				'required'  => false,
		))
		->add('multivalued', 'checkbox', array(
				'label'     => 'Multivalued',
				'required'  => false,
		))
		->add('omnitnorm', 'checkbox', array(
				'label'     => 'Omnit Norms',
				'required'  => false,
		))
		->add('stored', 'checkbox', array(
				'label'     => 'Stored',
				'required'  => false,
		))
		->getForm();
		
		
		$defaultData = array('name' => 'bonjour');
		$addFieldForm = $this->createFormBuilder($defaultData)
		->add('name', 'text')
		->add('email', 'text')
		->add('message', 'text')
		->add('fieldsType', 'choice', array(
				'choices'   => array('type 1' => 'Male', 'type 2' => 'Female'),
				'required'  => false,
		))
		->add('indexed', 'checkbox', array(
				'label'     => 'indexed',
				'required'  => false,
		))
		->add('stored', 'checkbox', array(
				'label'     => 'Stored',
				'required'  => false,
		))
		->add('multivalued', 'checkbox', array(
				'label'     => 'Multivalued',
				'required'  => false,
		))
		->add('omnitnorm', 'checkbox', array(
				'label'     => 'Omnit Norms',
				'required'  => false,
		))
		->add('stored', 'checkbox', array(
				'label'     => 'Stored',
				'required'  => false,
		))
		->getForm();
		
		
		
		
		
		if ($request->isMethod('POST')) {
			$form->bind($request);
		
			// data is an array with "name", "email", and "message" keys
			$data = $form->getData();
			return new Response($data['name']."  email  ");
		}
		
		return $this->render('AdministrationBundle:Default:analyzers.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	public function filtersAction()
	{
		return $this->render('AdministrationBundle:Default:filters.html.twig');
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
		return $this->render('AdministrationBundle:Default:dashboard.html.twig');
	}
	
	public function contactAction(Request $request)
	{
		$defaultData = array('name' => 'bonjour');
		$form = $this->createFormBuilder($defaultData)
		->add('name', 'text')
		->add('email', 'text')
		->add('message', 'text')
		->add('fieldsType', 'choice', array(
				'choices'   => array('type 1' => 'Male', 'type 2' => 'Female'),
				'required'  => false,
		))
		->add('indexed', 'checkbox', array(
				'label'     => 'indexed',
				'required'  => false,
		))
		->add('stored', 'checkbox', array(
				'label'     => 'Stored',
				'required'  => false,
		))
		->add('multivalued', 'checkbox', array(
				'label'     => 'Multivalued',
				'required'  => false,
		))
		->add('omnitnorm', 'checkbox', array(
				'label'     => 'Omnit Norms',
				'required'  => false,
		))
		->add('stored', 'checkbox', array(
				'label'     => 'Stored',
				'required'  => false,
		))
		->getForm();
		
		
		
		
		if ($request->isMethod('POST')) {
			$form->bind($request);
		
			// data is an array with "name", "email", and "message" keys
			$data = $form->getData();
			return new Response($data['fieldsType']."  email  ".$data['stored']);
		}
		
		return $this->render('AdministrationBundle:Default:new.html.twig', array(
				'form' => $form->createView(),
		));
	
		
	}



	
}