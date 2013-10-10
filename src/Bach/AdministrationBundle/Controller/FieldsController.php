<?php
namespace Bach\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;

use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class FieldsController extends Controller
{

    /**
     * Refresh
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        $xmlp = $session->get('xmlP');
        if ($request->isMethod('GET')) {
            $form = $this->createForm(
                new FieldsForm($xmlp),
                new Fields($xmlp)
            );
        } else {
            $form = $this->submitAction($request, $xmlp);
        }
        return $this->render(
            'AdministrationBundle:Default:fields.html.twig',
            array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
            )
        );
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
