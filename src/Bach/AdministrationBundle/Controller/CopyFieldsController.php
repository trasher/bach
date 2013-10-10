<?php
namespace Bach\AdministrationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\CopyField;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\CopyFields;
use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\CopyFieldsForm;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

class CopyFieldsController extends Controller
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
                new CopyFieldsForm($xmlp),
                new CopyFields($xmlp)
            );
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $xmlp);
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render(
            'AdministrationBundle:Default:copyfields.html.twig',
            array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
            )
        );
    }

    public function addCopyFieldAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $xmlp = $this->session->get('xmlP');

        $copyField = new CopyField($xmlp);
        $form = $this->createFormBuilder($copyField)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save the new copy field into the schema.xml file of corresponding core
                $dynamicField->addField($xmlp);
                $xmlP->saveXML();
                return $this->redirect($this->generateUrl('administration_copyfields'));
            }
        }
    }

    public function removeCopyFieldsAction(Request $request)
    {

    }

    public function submitAction(Request $request, XMLProcess $xmlP)
    {
        $session = $request->getSession();
        $xmlp = $session->get('xmlP');
        $cf = new CopyFields($xmlp);
        $form = $this->createForm(new CopyFieldsForm(), $cf);
        $form->bind($request);
        if ($form->isValid()) {
            $cf->save($xmlp);
        }
        return $form;
    }
}
