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
     * Expositions list
     *
     * @return void
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository('ExposBundle:Exposition');

        $expos = $repository->findCurrent();

        return $this->render(
            'ExposBundle:Default:index.html.twig',
            array(
                'expos' => $expos
            )
        );
    }

    /**
     * Exposition page
     *
     * @param string $name Exposition name
     *
     * @return void
     */
    public function showAction($name)
    {
        $repository = $this->getDoctrine()->getRepository('ExposBundle:Exposition');

        $expos = $repository->findCurrent();
        $expo = $repository->findOneByUrl($name);

        $position = -1;
        foreach ( $expos as $ex ) {
            $position++;
            if ( $ex->getId() === $expo->getId() ) {
                break;
            }
        }

        return $this->render(
            'ExposBundle:Default:show.html.twig',
            array(
                'position'      => $position,
                'expos'         => $expos,
                'current_expo'  => $expo
            )
        );
    }
}
