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
        $xmlP = new XMLProcess('core0');
        $form = $this->createForm(new DynamicFieldsForm(), new DynamicFields($xmlP));
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
    
    public function addDynamicFieldAction(Request $request)
    {
        $dynamicField = new DynamicField();
        $form = $this->createForm(new DynamicFieldsForm(), $dynamicField);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $xmlP = new XMLProcess('core0');
                $dynamicField->addField($xmlP);
                $xmlP->saveXML();
                return $this->redirect($this->generateUrl('administration_dynamicfields'));
            }
        }
    }
    
    public function removeDynamicFieldsAction(Request $request)
    {
        
    }
    
    public function submitAction(Request $request)
    {
        $df = new DynamicFields();
        $form = $this->createForm(new DynamicFieldsForm(), $df);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $xmlP = new XMLProcess('core0');
                $df->save($xmlP);
                $xmlP->saveXML();
            }
        }
        $form = $this->createForm(new DynamicFieldsForm(), $df);
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
