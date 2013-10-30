<?php
/**
 * Bach virtual expositions controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\ExposBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Bach virtual expositions controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DefaultController extends Controller
{

    /**
     * Serve default page
     *
     * @param string $name Exposition name
     *
     * @return void
     */
    public function indexAction($name)
    {
        return $this->render(
            'ExposBundle:Default:index.html.twig',
            array(
                'name' => $name
            )
        );
    }
}
