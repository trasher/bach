<?php
/**
 * Bach fields controller
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

use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\FieldsForm;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\Fields;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bach\AdministrationBundle\Entity\SolrSchema\XMLProcess;

/**
 * Bach fields controller
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
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
        $f = new Fields();
        $form = $this->createForm(new FieldsForm(), $f);
        $form->bind($request);
        if ($form->isValid()) {
            $f->save($xmlP);
        }
        return $form;
    }
}
