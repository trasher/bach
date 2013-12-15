<?php
/**
 * Bach geoloc controller
 *
 * PHP version 5
 *
 * @category Geoloc
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bach\IndexationBundle\Entity\Toponym;
use Bach\IndexationBundle\Entity\Geoloc;

/**
 * Bach geoloc controller
 *
 * PHP version 5
 *
 * @category Geoloc
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class GeolocController extends Controller
{
    /**
     * Standard Bach geoloc for a string
     *
     * @param string $name Toponym name
     *
     * @return JsonResponse
     */
    public function toponymAction($name)
    {
        $toponym = new Toponym($name);
        $nominatim = $this->container->get('bach.indexation.Nominatim');

        $found = array();
        if ( $toponym->canBeLocalized() ) {
            $result = $nominatim->proceed($toponym, false);

            if ( $result !== false ) {
                if ( !is_array($result) ) {
                    $result = array($result);
                }

                foreach ( $result as $r ) {
                    $ent = new Geoloc();
                    $ent->hydrate($toponym, $r);
                    $found[] = $ent->toArray();
                }
            }
        }

        if ( !$toponym->canBeLocalized() || count($found) < 3 ) {
            //try raw search
            $result = null;
            if ( $toponym->getSpecificName() !== null ) {
                $result = $nominatim->rawProceed($toponym->getSpecificName());
            } else if ( $toponym->getName() !== null ) {
                $result = $nominatim->rawProceed($toponym->getName());
            } else {
                $result = $nominatim->rawProceed($name);
            }

            if ( $result !== false ) {
                if ( !is_array($result) ) {
                    $result = array($result);
                }

                foreach ( $result as $r ) {
                    $ent = new Geoloc();
                    $ent->hydrate($toponym, $r);
                    $found[] = $ent->toArray();
                }
            }
        }

        $response = new JsonResponse();
        $response->setData($found);
        return $response;
    }

    /**
     * Raw Bach geoloc for a string
     *
     * @param string $name Name
     *
     * @return JsonResponse
     */
    public function rawAction($name)
    {
        $toponym = new Toponym($name);
        $nominatim = $this->container->get('bach.indexation.Nominatim');

        $found = array();
        $result = $nominatim->rawProceed($name);

        if ( $result !== false ) {
            foreach ( $result as $r ) {
                $ent = new Geoloc();
                $ent->hydrate($toponym, $r);
                $found[] = $ent->toArray();
            }
        }

        $response = new JsonResponse();
        $response->setData($found);
        return $response;
    }

    /**
     * Store a location
     *
     * @return JsonResponse
     */
    public function storeAction()
    {
        $request = $this->getRequest();
        $indexed_name = $request->get('indexed_name');

        $data = array(
            'boundingbox'   => $request->get('bbox'),
            'geojson'       => $request->get('geojson'),
            'lat'           => $request->get('lat'),
            'lon'           => $request->get('lon'),
            'name'          => $request->get('name'),
            'osm_id'        => $request->get('osm_id'),
            'place_id'      => $request->get('place_id'),
            'type'          => $request->get('type')
        );

        $toponym = new Toponym($indexed_name);

        $ent = new Geoloc();
        $ent->hydrate($toponym, $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ent);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(
            array(
                'success'   => true,
                'name'      => $indexed_name
            )
        );
        return $response;
    }
}
