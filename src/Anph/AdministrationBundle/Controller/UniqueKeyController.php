<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\UniqueKeyForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\UniqueKey;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class UniqueKeyController extends Controller
{
    public function refreshAction()
    {
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(new UniqueKeyForm(), new UniqueKey($session->get('xmlP')));
        return $this->render('AdministrationBundle:Default:uniquekey.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function saveAction()
    {
        $session = $this->getRequest()->getSession();
        $uk = new UniqueKey();
        $form = $this->createFormBuilder($uk)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $session->get('xmlP')->saveXML();
            }
        }
        return $this->render('AdministrationBundle:Default:uniquekey.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
}
