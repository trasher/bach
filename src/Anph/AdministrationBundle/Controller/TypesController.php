<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Types;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\TypesForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class TypesController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $types =  new Types($xmlP);
        $form = $this->createForm(new TypesForm(), $types);
        return $this->render('AdministrationBundle:Default:fieldstype.html.twig', array(
                'form' => $form->createView()
        ));
    }
    
    public function addTypeFieldAction(Request $request)
    {
        $typeField = new TypeField();
        $form = $this->createFormBuilder($typeField)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $xmlP = new XMLProcess('core0');
                $typeField->addField($xmlP);
                $xmlP->saveXML();
                return $this->redirect($this->generateUrl('administration_dynamicfields'));
            }
        }
    }
    
    public function removeTypeFieldsAction(Request $request)
    {
    
    }
    
    public function saveAction()
    {
        $types = new Types();
        $form = $this->createFormBuilder($types)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $xmlP->saveXML();
                return $this->redirect($this->generateUrl('administration_fieldstype'));
            }
        }
    }
}