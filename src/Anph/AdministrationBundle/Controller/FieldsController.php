<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class FieldsController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
            $form = $this->createForm(new FieldsForm(), new Fields($session->get('xmlP')));
        } else {
            $form = $this->submitAction($request, $session->get('xmlP'));
        }
        return $this->render('AdministrationBundle:Default:fields.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
                ));
    }
    
    private function submitAction(Request $request, XMLProcess $xmlP)
    {
        $f = new Fields();
        $form = $this->createForm(new FieldsForm(), $f);
        $form->bind($request);
        if ($form->isValid()) {
            $f->save($xmlP);
        }
        return $form;
    }
}
