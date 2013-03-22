<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\AnalyzersForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Analyzers;
use Anph\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AnalyzersController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
            $form = $this->createForm(new AnalyzersForm(), new Analyzers($session->get('xmlP')));
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $session->get('xmlP'));
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render('AdministrationBundle:Default:analyzers.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function submitAction(Request $request, XMLProcess $xmlP)
    {
        $session = $request->getSession();
        $a = new Analyzers();
        $form = $this->createForm(new AnalyzersForm(), $a);
        $form->bind($request);
        if ($form->isValid()) {
            // If the data is valid, we save new field into the schema.xml file of corresponding core
            $a->save($session->get('xmlP'));
        }
        return $form;
    }
}
