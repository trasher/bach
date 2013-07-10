<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\CopyField;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\CopyFields;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\CopyFieldsForm;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class CopyFieldsController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
            $form = $this->createForm(new CopyFieldsForm(), new CopyFields($session->get('xmlP')));
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $session->get('xmlP'));
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render('AdministrationBundle:Default:copyfields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function addCopyFieldAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $copyField = new CopyField();
        $form = $this->createFormBuilder($copyField)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save the new copy field into the schema.xml file of corresponding core
                $xmlP = $this->session->get('xmlP');
                $dynamicField->addField($xmlP);
                $xmlP->saveXML();
                return $this->redirect($this->generateUrl('administration_copyfields'));
            }
        }
    }
    
    public function removeCopyFieldsAction(Request $request)
    {
    
    }
    
    public function submitAction(Request $request, XMLProcess $xmlP)
    {
        $session = $request->getSession();
        $cf = new CopyFields();
        $form = $this->createForm(new CopyFieldsForm(), $cf);
        $form->bind($request);
        if ($form->isValid()) {
            $cf->save($session->get('xmlP'));
        }
        return $form;
    }
}
