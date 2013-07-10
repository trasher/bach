<?php
namespace Bach\AdministrationBundle\Controller;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\FieldType;

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\Types;
use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\TypesForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class TypesController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
            $form = $this->createForm(new TypesForm(), new Types($session->get('xmlP')));
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $session->get('xmlP'));
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
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
    
    private function submitAction(Request $request, XMLProcess $xmlP)
    {
        $session = $this->getRequest()->getSession();
        $t = new Types();
        $form = $this->createForm(new TypesForm(), $t);
        $form->bind($request);
        if ($form->isValid()) {
            // If the data is valid, we save new field into the schema.xml file of corresponding core
            $t->save($xmlP);
        }
        return $form;
    }
}