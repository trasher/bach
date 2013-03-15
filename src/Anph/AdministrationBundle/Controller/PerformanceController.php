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
        $form = $this->createForm(new PerformanceForm(), new Performance($session->get('coreName')));
        return $this->render('AdministrationBundle:Default:performance.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function submitAction()
    {
        $session = $this->getRequest()->getSession();
        $perf = new Performance();
        $form = $this->createForm(new PerformanceForm(), $perf)->bind($this->getRequest());
        $perf->saveAll($session->get('coreName'));
        
        if ($form->isValid()) {
            $perf->saveAll($session->get('coreName'));
        }
        return $this->render('AdministrationBundle:Default:performance.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
}