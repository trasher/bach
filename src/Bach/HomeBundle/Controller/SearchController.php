<?php
/**
 * Bach search controller
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

use Bach\HomeBundle\Entity\SolariumQueryContainer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bach\HomeBundle\Entity\ViewParams;
use Bach\HomeBundle\Entity\SearchQueryFormType;
use Bach\HomeBundle\Entity\SearchQuery;
use Bach\HomeBundle\Entity\Comment;
use Bach\HomeBundle\Entity\BrowseFields;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Bach\HomeBundle\Entity\Filters;
use Bach\HomeBundle\Entity\TagCloud;
use Bach\HomeBundle\Entity\GeolocFields;
use Bach\HomeBundle\Service\SolariumQueryFactory;

/**
 * Bach search controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
abstract class SearchController extends Controller
{

    /**
     * Serve default page
     *
     * @param string  $query_terms Term(s) we search for
     * @param int     $page        Page
     * @param string  $facet_name  Display more terms in suggests
     * @param boolean $ajax        Fomr ajax call
     *
     * @return void
     */
    abstract public function indexAction($query_terms = null, $page = 1,
        $facet_name = null, $ajax = false
    );

    /**
     * Get common template variables
     *
     * @param ViewParams $view_params View parameters
     * @param int        $page        Current requested page
     *
     * @return array
     */
    protected function searchTemplateVariables($view_params, $page = 1)
    {
        $common_vars = $this->commonTemplateVariables();

        $tpl_vars = array(
            'page'              => $page,
            'show_pics'         => $view_params->showPics(),
            'show_map'          => $view_params->showMap(),
            'show_daterange'    => $view_params->showDaterange(),
            'view'              => $view_params->getView(),
            'results_order'     => $view_params->getOrder(),
            'map_facets_name'   => $this->mapFacetsName(),
            'q'                 => ''
        );

        return array_merge($common_vars, $tpl_vars);
    }

    /**
     * Get common template variables
     *
     * @return array
     */
    protected function commonTemplateVariables()
    {
        $show_maps = $this->container->getParameter('show_maps');
        $viewer_uri = $this->container->getParameter('viewer_uri');
        $covers_dir = $this->container->getParameter('covers_dir');

        $tpl_vars = array(
            'viewer_uri'    => $viewer_uri,
            'show_maps'     => $show_maps,
            'covers_dir'    => $covers_dir
        );

        return $tpl_vars;
    }

    /**
     * Handle geolocalization
     *
     * @param SolariumQueryFactory $factory   Query factory
     * @param array                &$tpl_vars Template variables
     * @param array                $fields    Fields list
     *
     * @return void
     */
    protected function handleGeoloc(SolariumQueryFactory $factory, &$tpl_vars,
        $fields = null
    ) {
        $show_maps = $this->container->getParameter('show_maps');
        if ( $show_maps ) {
            $request = $this->getRequest();
            $session = $request->getSession();

            if ( $fields === null ) {
                $gf = new GeolocFields();
                $gf = $gf->loadCloud(
                    $this->getDoctrine()->getManager()
                );
                $fields = $gf->getSolrFieldsNames();
            }

            $query = $factory->getQuery();
            $rs = null;

            if ( $query === null ) {
                $query = $this->get($this->entryPoint())->createSelect();
                $query->setQuery('*:*');
                $query->setStart(0)->setRows(0);

                $facetSet = $query->getFacetSet();
                $facetSet->setLimit(-1);
                $facetSet->setMinCount(1);
                foreach ( $fields as $field ) {
                    $facetSet->createFacetField($field)->setField($field);
                }

                $rs = $this->get($this->entryPoint())->select($query);
            } else {
                $rs = $factory->getResultset();
            }

            foreach ( $fields as $field ) {
                $map_facets[$field] = $rs->getFacetSet()->getFacet($field);
            }

            $session->set(
                $this->mapFacetsName(),
                $map_facets
            );
            $geojson = $factory->getGeoJson(
                $map_facets,
                $this->getDoctrine()
                    ->getRepository('BachIndexationBundle:Geoloc')
            );
            $tpl_vars['geojson'] = $geojson;
        }
    }

    /**
     * Get map facets session name
     *
     * @return string
     */
    abstract protected function mapFacetsName();

    /**
     * Get Solarium EntryPoint
     *
     * @return string
     */
    abstract protected function entryPoint();

    /**
     * Get geographical zones
     *
     * @param stirng $bbox Bounding box
     *
     * @return json
     */
    public function getZonesAction($bbox)
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $factory = $this->get("bach.home.solarium_query_factory");

        $facets_name = $request->get('facets_name');

        if ( !$facets_name ) {
            $facets_name = 'map_facets';
        }

        $geojson = $factory->getGeoJson(
            $session->get($facets_name),
            $this->getDoctrine()
                ->getRepository('BachIndexationBundle:Geoloc'),
            $bbox
        );

        $response = new Response($geojson);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * POST search destination for main form.
     *
     * Will take care of search terms, and reroute with proper URI
     *
     * @return void
     */
    abstract public function doSearchAction();

    /**
     * Suggests
     *
     * @return void
     */
    public function doSuggestAction()
    {
        $query = $this->get("solarium.client")->createSuggester();
        $query->setQuery(strtolower($this->getRequest()->get('q')));
        $query->setDictionary('suggest');
        $query->setOnlyMorePopular(true);
        $query->setCount(10);
        //$query->setCollate(true);
        $terms = $this->get("solarium.client")->suggester($query)->getResults();

        $suggestions = array();
        foreach ( $terms as $term ) {
            $suggestions = array_merge(
                $suggestions,
                $term->getSuggestions()
            );
        }

        return new JsonResponse($suggestions);
    }
}
