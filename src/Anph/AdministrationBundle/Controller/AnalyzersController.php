<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\AnalyzersForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Analyzers;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnalyzersController extends Controller
{
    public function refreshAction()
    {
        $xmlP = new XMLProcess('core0');
        $fields =  new Analyzers($xmlP);
        $form = $this->createForm(new AnalyzersForm(), $fields);
        return $this->render('AdministrationBundle:Default:analyzers.html.twig', array(
                'form' => $form->createView(),
        ));
    }
    
    public function saveAction()
    {
        $analyzers = new Analyzers();
        $form = $this->createFormBuilder($analyzers)->getForm();
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
