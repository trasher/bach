<?php
/**
 * Bach analysers controller
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

use Symfony\Component\HttpFoundation\Request;

use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\AnalyzersForm;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\Analyzers;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Bach analyzers controller
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class AnalyzersController extends Controller
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
                new AnalyzersForm(),
                new Analyzers($session->get('xmlP'))
            );
        } else {
            $btn = $request->request->get('submit');
            if (isset($btn)) {
                $form = $this->submitAction($request);
            } elseif (isset($btn)) {
                echo 'ELSIF';
            }
        }
        return $this->render(
            'AdministrationBundle:Default:analyzers.html.twig',
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
     * @param Request $request Request
     *
     * @return void
     */
    public function submitAction(Request $request)
    {
        $session = $request->getSession();
        $a = new Analyzers();
        $form = $this->createForm(new AnalyzersForm(), $a);
        $form->bind($request);
        if ($form->isValid()) {
            // If the data is valid, we save new field into the
            // schema.xml file of corresponding core
            $a->save($session->get('xmlP'));
        }
        return $form;
    }
}
