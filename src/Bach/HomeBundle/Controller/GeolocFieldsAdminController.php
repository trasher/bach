<?php
/**
 * Bach geolocalization fields admin controller (for Sonata Admin)
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Bach geolocalization fields admin controller
 *
 * PHP version 5
 *
 * @category Security
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class GeolocFieldsAdminController extends Controller
{

    /**
     * return the Response object associated to the edit action
     *
     * @param mixed $id Element id
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function editAction($id = null)
    {
        $geolocfields = $this->admin->getNewInstance();
        $geolocfields = $geolocfields->loadCloud(
            $this->getDoctrine()->getManager()
        );

        $id = $geolocfields->getId();
        $this->get('request')->attributes->set('id', $id);
        return parent::editAction($id);
    }
}

