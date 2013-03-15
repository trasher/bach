<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\AnalyzersForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Analyzers;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnalyzersController extends Controller
{
    public function refreshAction()
    {
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(new AnalyzersForm(), new Analyzers($session->get('xmlP')));
        return $this->render('AdministrationBundle:Default:analyzers.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function submitAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $a = new Analyzers();
        $form = $this->createForm(new AnalyzersForm(), $a);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the schema.xml file of corresponding core
                $a->save($session->get('xmlP'));
            }
        }
        return $this->render('AdministrationBundle:Default:analyzers.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
}
