<?php
namespace Bach\AdministrationBundle\Controller;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\DynamicField;

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\DynamicFieldsForm;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\DynamicFields;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class DynamicFieldsController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
            $form = $this->createForm(new DynamicFieldsForm(), new DynamicFields($session->get('xmlP')));
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $session->get('xmlP'));
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render('AdministrationBundle:Default:dynamicfields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    private function addDynamicFieldAction(Request $request)
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
    
    private function submitAction(Request $request, XMLProcess $xmlP)
    {
        $df = new DynamicFields();
        $form = $this->createForm(new DynamicFieldsForm(), $df);
        $form->bind($request);
        if ($form->isValid()) {
            $df->save($xmlP);
            $xmlP->saveXML();
        }
        return $form;
    }
}
