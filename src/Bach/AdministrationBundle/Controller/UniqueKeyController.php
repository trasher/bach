<?php
namespace Bach\AdministrationBundle\Controller;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\UniqueKey;

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\UniqueKeyForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class UniqueKeyController extends Controller
{
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        if ($request->isMethod('GET')) {
            $form = $this->createForm(new UniqueKeyForm(), new UniqueKey($session->get('xmlP')));
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $session->get('xmlP'));
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render('AdministrationBundle:Default:uniquekey.html.twig', array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
        ));
    }
    
    public function submitAction(Request $request, XMLProcess $xmlP)
    {
        $session = $request->getSession();
        $uk = new UniqueKey();
        $form = $this->createForm(new UniqueKeyForm(), $uk);
        $form->bind($request);
        if ($form->isValid()) {
            $uk->save($xmlP);
        }
        return $form;
    }
}
