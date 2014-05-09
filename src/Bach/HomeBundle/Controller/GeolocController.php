<?php
/**
 * Bach geoloc controller
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Geoloc
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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

        $repo = $this->getDoctrine()
            ->getRepository('BachIndexationBundle:Geoloc');
        $ent = $repo->findOneBy(
            array(
                'indexed_name' => $indexed_name
            )
        );

        if ( $ent === null ) {
            $ent = new Geoloc();
        }

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

    /**
     * Remove a geoloc from its indexed name
     *
     * @param string $name Name
     *
     * @return JsonResponse
     */
    public function removeAction($name)
    {
        $repo = $this->getDoctrine()->getRepository('BachIndexationBundle:Geoloc');
        $geoloc =  $repo->findOneBy(array('indexed_name' => $name));

        $em = $this->getDoctrine()->getManager();
        $em->remove($geoloc);
        $em->flush();

        $response = new JsonResponse();
        $response->setData(
            array(
                'success'   => true,
                'name'      => $geoloc->getIndexedName()
            )
        );
        return $response;
    }
}
