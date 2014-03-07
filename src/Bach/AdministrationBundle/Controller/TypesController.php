<?php
/**
 * Bach types controller
 *
 * PHP version 5
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\AdministrationBundle\Controller;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\FieldType;

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\Types;
use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\TypesForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

/**
 * Bach types controller
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class TypesController extends Controller
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
        if ($request->isMethod('GET')) {
            $form = $this->createForm(
                new TypesForm(),
                new Types($session->get('xmlP'))
            );
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request, $session->get('xmlP'));
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render(
            'AdministrationBundle:Default:fieldstype.html.twig',
            array(
                'form'      => $form->createView(),
                'coreName'  => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
            )
        );
    }

    /**
     * Add type field
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function addTypeFieldAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $ft = new FieldType();
        $form = $this->createFormBuilder($ft)->getForm();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // If the data is valid, we save new field into the
                // schema.xml file of corresponding core
                $xmlP = $session->get('xmlP');
                $ft->addField($xmlP);
                $xmlP->saveXML();
            }
        }
        return $this->render(
            'AdministrationBundle:Default:fieldstype.html.twig',
            array(
                'form'      => $form->createView(),
                'coreName'  => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
            )
        );
    }

    /**
     * Submit
     *
     * @param Request    $request Request
     * @param XMLProcess $xmlP    XMLProcess
     *
     * @return void
     */
    public function submitAction(Request $request, XMLProcess $xmlP)
    {
        $t = new Types();
        $form = $this->createForm(new TypesForm(), $t);
        $form->bind($request);
        if ($form->isValid()) {
            // If the data is valid, we save new field into the
            // schema.xml file of corresponding core
            $t->save($xmlP);
        }
        return $form;
    }
}
