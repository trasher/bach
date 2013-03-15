<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\DynamicField;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\DynamicFieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\DynamicFields;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class DynamicFieldsController extends Controller
{
    public function refreshAction()
    {
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(new DynamicFieldsForm(), new DynamicFields($session->get('xmlP')));
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function addDynamicFieldAction(Request $request)
    {
        $df = new DynamicField();
        $form = $this->createForm(new DynamicFieldsForm(), $df);
        //$form->bind($request);
        if ($form->isValid()) {
            // If the data is valid, we save new field into the schema.xml file of corresponding core
            /*$xmlP = $session->get('xmlP');
            $df->addField($xmlP);
            $xmlP->saveXML();*/
        }
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function removeDynamicFieldsAction(Request $request)
    {
        
    }
    
    public function submitAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        /*if (isset($request->request->get('add'))) {
            echo 'EXIST';
        } else {
            echo 'NOT EXIST';
        }*/
        /*$df = new DynamicFields();
        $form = $this->createForm(new DynamicFieldsForm(), $df);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $xmlP = new XMLProcess('core0');
                $df->save($xmlP);
                $xmlP->saveXML();
            }
        }*/
        $form = $this->createForm(new DynamicFieldsForm(), new DynamicFields($session->get('xmlP')));
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
}
