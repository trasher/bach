<?php
/**
 * Bach dynamic fields controller
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

use Bach\AdministrationBundle\Entity\Helpers\FormObjects\DynamicField;

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\DynamicFieldsForm;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\DynamicFields;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

/**
 * Bach dynamic fields controller
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DynamicFieldsController extends Controller
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
                new DynamicFieldsForm($xmlp),
                new DynamicFields($xmlp)
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
            'AdministrationBundle:Default:dynamicfields.html.twig',
            array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
            )
        );
    }

    /**
     * Add dynamic field
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function addDynamicFieldAction(Request $request)
    {
        $df = new DynamicField();
        $form = $this->createForm(new DynamicFieldsForm(), $df);
        //$form->bind($request);
        if ($form->isValid()) {
            // If the data is valid, we save new field into the
            // schema.xml file of corresponding core
            /*$xmlP = $session->get('xmlP');
            $df->addField($xmlP);
            $xmlP->saveXML();*/
        }
        return $this->render(
            'AdministrationBundle:Default:dynamicfields.html.twig',
            array(
                'form' => $form->createView(),
                'coreName' => $session->get('coreName'),
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
        $df = new DynamicFields($xmlP);
        $form = $this->createForm(new DynamicFieldsForm($xmlP), $df);
        $form->bind($request);
        if ($form->isValid()) {
            $df->save($xmlP);
            $xmlP->saveXML();
        }
        return $form;
    }
}
