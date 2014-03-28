<?php
/**
 * Bach geoloc admin controller (for Sonata Admin)
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
use Bach\IndexationBundle\Entity\Toponym;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Bach geoloc admin controller
 *
 * PHP version 5
 *
 * @category Security
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class GeolocAdminController extends Controller
{
    /**
     * Handle missing geolocalized entries
     *
     * @return void
     */
    public function geolocMissingAction()
    {

        $doctrine = $this->container->get('doctrine');

        $repo = $doctrine->getRepository('BachIndexationBundle:EADIndexes');
        $qb = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.name')
            ->leftJoin(
                'BachIndexationBundle:Geoloc',
                'g',
                'WITH',
                'a.name = g.indexed_name'
            )
            ->where('a.type = :type')
            ->andWhere('g.indexed_name IS NULL')
            ->orWhere('g.found = false')
            ->setParameter('type', 'cGeogname');

        $query = $qb->getQuery();
        $bdd_places = $query->getResult();

        $repo = $doctrine->getRepository(
            'BachIndexationBundle:MatriculesFileFormat'
        );
        $qb = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.lieu_naissance AS name')
            ->leftJoin(
                'BachIndexationBundle:Geoloc',
                'g',
                'WITH',
                'a.lieu_naissance = g.indexed_name'
            )
            ->where('g.indexed_name IS NULL')
            ->orWhere('g.found = false');

        $query = $qb->getQuery();
        $bdd_places = array_merge($bdd_places, $query->getResult());

        $repo = $doctrine->getRepository(
            'BachIndexationBundle:MatriculesFileFormat'
        );
        $qb = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.lieu_enregistrement as name')
            ->leftJoin(
                'BachIndexationBundle:Geoloc',
                'g',
                'WITH',
                'a.lieu_enregistrement = g.indexed_name'
            )
            ->where('g.indexed_name IS NULL')
            ->orWhere('g.found = false');

        $query = $qb->getQuery();
        $bdd_places = array_merge($bdd_places, $query->getResult());

        $places = array();
        foreach ( $bdd_places as $p ) {
            //create toponyms & dedup
            if ( !isset($places[$p['name']]) ) {
                try {
                    $places[$p['name']]= new Toponym($p['name']);
                } catch ( \RuntimeException $e ) {
                    //pass
                }
            }
        }

        /*$nominatim = $this->container->get('bach.indexation.Nominatim');
        $found = array();

        foreach ( $places as $orig=>$toponym ) {
            if ( $toponym->canBeLocalized() ) {
                $result = $nominatim->proceed($toponym);

                if ( $result !== false ) {
                    if ( !is_array($result) ) {
                        $result = (array)$result;
                    }

                    foreach ( $result as $r ) {
                        $ent = new Geoloc();
                        $ent->hydrate($toponym, $result);
                        $found[$origin][] = $ent;
                    }
                }
            }
        }*/

        return $this->render(
            'BachHomeBundle:Admin:geoloc_missing.html.twig',
            array(
                'missing'   => $places
            )
        );
    }

    /**
     * Visualize on map to edit or delete entries
     *
     * @return void
     */
    public function geolocVisualizeAction()
    {
        $known_types = $this->container->getParameter('bach.types');
        foreach ( $known_types as $type ) {
            $geoloc = null;

            switch ( $type ) {
            case 'ead':
                $gf = new \Bach\HomeBundle\Entity\GeolocMainFields;
                $gf = $gf->loadDefaults(
                    $this->getDoctrine()->getManager()
                );
                $geoloc = $gf->getSolrFieldsNames();
                break;
            case 'matricules':
                $gf = new \Bach\HomeBundle\Entity\GeolocMatriculesFields;
                $gf = $gf->loadDefaults(
                    $this->getDoctrine()->getManager()
                );
                $geoloc = $gf->getSolrFieldsNames();
                break;
            default:
                continue;
                break;
            }

            if ( $geoloc !== null ) {
                $client = $this->get('solarium.client.' . $type);
                $query = $client->createSelect();
                $query->setQuery('*:*');
                $query->setStart(0)->setRows(0);

                $facetSet = $query->getFacetSet();
                $facetSet->setLimit(-1);
                $facetSet->setMinCount(1);

                foreach ( $geoloc as $field ) {
                    $facetSet->createFacetField($field)
                        ->setField($field);
                }

                try {
                    $rs = $client->select($query);

                    $facetset = $rs->getFacetSet();
                    foreach ( $geoloc as $field ) {
                        if ( !isset($map_facets[$field]) ) {
                            $map_facets[$field] = $facetset->getFacet($field);
                        } else {
                            $map_facets[$field] = array_merge(
                                $map_facets[$field],
                                $facetset->getFacet($field)
                            );
                        }
                    }
                } catch ( HttpException $ex ) {
                    //empty catch
                }
            }
        }

        $factory = $this->get("bach.home.solarium_query_factory");
        $geojson = $factory->getGeoJson(
            $map_facets,
            $this->getDoctrine()
                ->getRepository('BachIndexationBundle:Geoloc')
        );

        return $this->render(
            'BachHomeBundle:Admin:geoloc_visualize.html.twig',
            array('geojson' => $geojson)
        );
    }
}
