<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\FieldType;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Types;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\TypesForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class TypesController extends Controller
{
    public function refreshAction()
    {
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(new TypesForm(), new Types($session->get('xmlP')));
        return $this->render('AdministrationBundle:Default:fieldstype.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function addTypeFieldAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $ft = new FieldType();
        $form = $this->createFormBuilder($typeField)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $xmlP = $session->get('xmlP');
                $ft->addField($xmlP);
                $xmlP->saveXML();
            }
        }
        return $this->render('AdministrationBundle:Default:fieldstype.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function removeTypeFieldsAction(Request $request)
    {
    
    }
    
    public function saveAction()
    {
        $session = $this->getRequest()->getSession();
        $types = new Types();
        $form = $this->createFormBuilder($types)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $session->get('xmlP')->saveXML();
            }
        }
        return $this->render('AdministrationBundle:Default:fieldstype.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
}