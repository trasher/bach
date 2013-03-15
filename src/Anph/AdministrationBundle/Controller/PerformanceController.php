<?php
namespace Anph\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Anph\AdministrationBundle\Entity\Helpers\FormBuilders\PerformanceForm;
use Anph\AdministrationBundle\Entity\Helpers\FormObjects\Performance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PerformanceController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        if ($request->isMethod('POST')) {
            return $this->changeAction($request);
        } else {
            $form = $this->createForm(new PerformanceForm(), new Performance('core0'));
            return $this->render('AdministrationBundle:Default:performance.html.twig', array(
                    'form' => $form->createView(),
                    'coreName' => $session->get('coreName'),
                    'coreNames' => $session->get('coreNames')
            ));
        }
    }
    
    private function changeAction(Request $request)
    {
        // TODO voir la validation du formulaire, mettre le code au propre
        $perf = new Performance();
        $form = $this->createForm(new PerformanceForm(), $perf)->bind($request);
        $perf->saveAll('core0');
        
        if ($form->isValid()) {
            $perf->saveAll('core0');
            return $this->redirect($this->generateUrl('task_success'));
        } else {
            $form = $this->createForm(new PerformanceForm(), new Performance('core0'));
            return $this->render('AdministrationBundle:Default:performance.html.twig', array(
                    'form' => $form->createView()
            ));
        }
    }
}