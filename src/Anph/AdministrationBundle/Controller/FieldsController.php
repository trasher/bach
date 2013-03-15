<?php
namespace Anph\AdministrationBundle\Controller;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class FieldsController extends Controller
{
    public function refreshAction()
    {
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(new FieldsForm(), new Fields($session->get('xmlP')));
        return $this->render('AdministrationBundle:Default:fields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
                ));
    }
    
    public function submitAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $f = new Fields();
        $form = $this->createForm(new FieldsForm(), $f);
        if ($request->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                // We save the modifications into the schema.xml file of corresponding core
                $f->save($session->get('xmlP'));
            }
        }
        return $this->render('AdministrationBundle:Default:fields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
                ));
    }
}
