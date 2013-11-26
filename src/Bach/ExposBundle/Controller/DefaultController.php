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
     * @param string $expo Exposition name
     *
     * @return void
     */
    public function showAction($expo)
    {
        $repository = $this->getDoctrine()->getRepository('ExposBundle:Exposition');

        $expos = $repository->findCurrent();
        $expo = $repository->findOneByUrl($expo);

        if ( $expo ) {
            $position = -1;
            foreach ( $expos as $ex ) {
                $position++;
                if ( $ex->getId() === $expo->getId() ) {
                    break;
                }
            }

            return $this->render(
                'ExposBundle:Default:show_expo.html.twig',
                array(
                    'position'      => $position,
                    'expos'         => $expos,
                    'current_expo'  => $expo
                )
            );
        } else {
            throw $this->createNotFoundException(
                _('Cannot found requested exposition!')
            );
        }
    }

    /**
     * Room page
     *
     * @param string $expo Exposition name
     * @param string $room Room name
     *
     * @return void
     */
    public function showRoomAction($expo, $room)
    {
        $repository = $this->getDoctrine()->getRepository('ExposBundle:Room');
        $room = $repository->findOneByUrl($room);

        $repository = $this->getDoctrine()->getRepository('ExposBundle:Exposition');
        $expos = $repository->findCurrent();
        $expo = $repository->findOneByUrl($expo);

        $position = -1;
        foreach ( $expos as $ex ) {
            $position++;
            if ( $ex->getId() === $expo->getId() ) {
                break;
            }
        }

        return $this->render(
            'ExposBundle:Default:show_room.html.twig',
            array(
                'position'      => $position,
                'expos'         => $expos,
                'current_expo'  => $expo,
                'current_room'  => $room
            )
        );
    }

    /**
     * Panel page
     *
     * @param string $expo  Exposition name
     * @param string $room  Room name
     * @param string $panel Panel name
     *
     * @return void
     */
    public function showPanelAction($expo, $room, $panel)
    {
        $repository = $this->getDoctrine()->getRepository('ExposBundle:Panel');
        $panel = $repository->findOneByUrl($panel);

        $room = $panel->getRoom();

        $repository = $this->getDoctrine()->getRepository('ExposBundle:Exposition');
        $expos = $repository->findCurrent();
        $expo = $repository->findOneByUrl($expo);

        $position = -1;
        foreach ( $expos as $ex ) {
            $position++;
            if ( $ex->getId() === $expo->getId() ) {
                break;
            }
        }

        return $this->render(
            'ExposBundle:Default:show_panel.html.twig',
            array(
                'position'      => $position,
                'expos'         => $expos,
                'current_expo'  => $expo,
                'current_room'  => $room,
                'current_panel' => $panel
            )
        );
    }


}
