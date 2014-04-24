<?php
/**
 * Bach core administration controller
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

use Bach\AdministrationBundle\Entity\Helpers\ViewObjects\CoreStatus;
use Bach\AdministrationBundle\Entity\Helpers\FormBuilders\CoreCreationForm;
use Symfony\Component\HttpFoundation\Request;
use Bach\AdministrationBundle\Entity\Helpers\FormObjects\CoreCreation;
use Bach\AdministrationBundle\Entity\SolrCore\SolrCoreAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Bach core administration controller
 *
 * @category Administration
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class CoreAdminController extends Controller
{

    /**
     * New Solr core creation page
     *
     * @return void
     */
    public function newCoreAction()
    {
        $session = $this->getRequest()->getSession();
        $form = $this->createForm(
            new CoreCreationForm(
                $this->getDoctrine(),
                $this->container->getParameter('database_name')
            )
        );
        return $this->render(
            'AdministrationBundle:Default:newcore.html.twig',
            array(
                'form'      => $form->createView(),
                'coreName'  => $session->get('coreName'),
                'coreNames' => $session->get('coreNames')
            )
        );
    }

    /**
     * Refresh core informations
     *
     * @param Request $request Request
     *
     * @return void
     */
    public function refreshAction(Request $request)
    {
        $session = $request->getSession();
        $configreader = $this->container->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);

        if (!$request->isMethod('GET')) {
            $btn = $request->request->get('createCoreOk');
            if (isset($btn)) {
                $this->_createCore($request);
            }
            return $this->redirect(
                $this->generateUrl('administration_dashboard')
            );
        }
        return $this->render(
            'AdministrationBundle:Default:coreadmin.html.twig',
            array(
                'coreName' => $session->get('coreName'),
                'coreNames' => $session->get('coreNames'),
                'coreStatus' => new CoreStatus(
                    $sca,
                    $session->get('coreName')
                )
            )
        );
    }

    /**
     * Create a new core
     *
     * @param Request $request Request
     *
     * @return Form
     */
    private function _createCore(Request $request)
    {
        $session = $request->getSession();
        $configreader = $this->container->get('bach.administration.configreader');
        $sca = new SolrCoreAdmin($configreader);
        $cc = new CoreCreation();
        $form = $this->createForm(
            new CoreCreationForm(
                $this->getDoctrine(),
                $this->container->getParameter('database_name')
            ),
            $cc
        );
        $form->bind($request);

        $em = $this->getDoctrine()->getManager();
        $orm_name = 'Bach\IndexationBundle\Entity';
        switch ( $cc->core ) {
        case 'EADFileFormat':
            $orm_name .= '\EADFileFormat';
            break;
        case 'MatriculesFileFormat':
            $orm_name .= '\MatriculesFileFormat';
            break;
        default:
            throw new \RuntimeException(
                str_replace(
                    '%type',
                    $cc->core,
                    _('Unkwown type %type')
                )
            );
            break;
        }

        $db_params = $sca->getJDBCDatabaseParameters(
            $this->getContainer()->getParameter('database_driver'),
            $this->getContainer()->getParameter('database_host'),
            $this->getContainer()->getParameter('database_port'),
            $this->getContainer()->getParameter('database_name'),
            $this->getContainer()->getParameter('database_user'),
            $this->getContainer()->getParameter('database_password')
        );

        $result = $sca->create(
            $cc->core,
            $cc->name,
            $cc->core,
            $orm_name,
            $em,
            $db_params
        );

        if ( count($sca->getErrors()) > 0) {
            foreach ( $sca->getErrors() as $w ) {
                $this->get('session')->getFlashBag()->add('errors', $w);
            }
        }

        if ( count($sca->getWarnings()) > 0) {
            foreach ( $sca->getWarnings() as $w ) {
                $this->get('session')->getFlashBag()->add('warnings', $w);
            }
        }

        if ($result != false && $result->isOk()) {
            $coreNames = $sca->getStatus()->getCoreNames();
            $session->set('coreNames', $coreNames);
        }
        return $form;
    }
}
