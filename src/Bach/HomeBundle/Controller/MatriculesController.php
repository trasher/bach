<?php
/**
 * Bach matricules controller
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Bach\HomeBundle\Form\Type\MatriculesType;
use Bach\HomeBundle\Entity\SolariumQueryContainer;
use Bach\HomeBundle\Entity\Filters;
use Bach\HomeBundle\Entity\ViewParams;
use Bach\HomeBundle\Entity\GeolocFields;
use Bach\HomeBundle\Entity\Facets;
use Bach\HomeBundle\Entity\SearchQueryFormType;
use Bach\HomeBundle\Entity\SearchQuery;

/**
 * Bach matricules controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class MatriculesController extends SearchController
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
    public function indexAction($query_terms = null, $page = 1,
        $facet_name = null, $ajax = false
    ) {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ( $query_terms !== null ) {
            $query_terms = urldecode($query_terms);
        }

        /** Manage view parameters */
        $view_params = $session->get('matricules_view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        //take care of user view params
        $_cook = null;
        if ( isset($_COOKIE['bach_matricules_view_params']) ) {
            $view_params->bindCookie('bach_matricules_view_params');
        }

        //set current view parameters according to request
        $view_params->bind($request);

        //store new view parameters
        $session->set('matricules_view_params', $view_params);

        $tpl_vars = $this->searchTemplateVariables($view_params, $page);

        $show_maps = $this->container->getParameter('show_maps');

        $geoloc = array();
        if ( $show_maps ) {
            $geoloc = array(
                'lieu_naissance',
                'lieu_enregistrement'
            );
        }

        if ( $view_params->advancedSearch() ) {
            $form = $this->createForm(
                new MatriculesType(),
                null
            );
            $tpl_vars['search_path'] = 'bach_matricules';
        } else {
            $form = $this->createForm(
                new SearchQueryFormType(),
                null
            );
            $tpl_vars['search_path'] = 'bach_matricules_do_search';
        }

        $form->handleRequest($request);
        $data = $form->getData();

        $resultCount = null;
        $searchResults = null;

        $factory = $this->get("bach.matricules.solarium_query_factory");
        $factory->setGeolocFields($geoloc);

        if ( $view_params->advancedSearch() && count($data) > 0
            || !$view_params->advancedSearch() && $query_terms !== null
        ) {
            $container = new SolariumQueryContainer();

            if ( $view_params->advancedSearch() ) {
                $container->setField('adv_matricules', $data);
            } else {
                $container->setField('matricules', $query_terms);
            }
            $container->setFilters(new Filters());
            $factory->prepareQuery($container);

            $classeFacet = new Facets();
            $classeFacet->setSolrFieldName('classe');

            $searchResults = $factory->performQuery(
                $container,
                array(
                    $classeFacet
                ),
                $geoloc
            );

            $hlSearchResults = $factory->getHighlighting();
            $scSearchResults = $factory->getSpellcheck();
            $resultCount = $searchResults->getNumFound();

            $tpl_vars['searchResults'] = $searchResults;
            $tpl_vars['hlSearchResults'] = $hlSearchResults;
            $tpl_vars['scSearchResults'] = $scSearchResults;
            $tpl_vars['totalPages'] = ceil(
                $resultCount/$view_params->getResultsbyPage()
            );

            $facets = array();
            $facetset = $searchResults->getFacetSet();

            $suggestions = null;
            if ( $view_params->advancedSearch() ) {
                $suggestions = $factory->getSuggestions(implode(' ', $data));
            } else {
                $suggestions = $factory->getSuggestions($query_terms);
            }
        }

        $slider_dates = $factory->getSliderDates(
            new Filters(),
            array(
                'date_begin' => 'date_enregistrement'
            )
        );

        if ( is_array($slider_dates) ) {
            $tpl_vars = array_merge($tpl_vars, $slider_dates);
        }

        $by_year = $factory->getResultsByYear('date_enregistrement');
        $tpl_vars['by_year'] = $by_year;

        if ( $this->container->get('kernel')->getEnvironment() == 'dev'
            && isset($factory) && $factory->getRequest() !== null
        ) {
            //let's pass Solr raw query to template
            $tpl_vars['solr_qry'] = $factory->getRequest()->getUri();
        }

        $this->handleGeoloc(
            $factory,
            $tpl_vars,
            array(
                'lieu_naissance',
                'lieu_enregistrement'
            )
        );

        /*if ( $show_maps ) {
            $session->set('matricules_map_facets', $map_facets);
            $geojson = $factory->getGeoJson(
                $map_facets,
                $this->getDoctrine()
                    ->getRepository('BachIndexationBundle:Geoloc')
            );
            $tpl_vars['geojson'] = $geojson;
        }*/

        if ( $view_params->advancedSearch() ) {
            $tpl_vars['adv_form'] = $form->createView();
        } else {
            $tpl_vars['form'] = $form->createView();
        }

        return $this->render(
            'BachHomeBundle:Matricules:search_form.html.twig',
            array_merge(
                array(
                    'resultStart'       => 1,
                    'resultEnd'         => $resultCount,
                    'resultCount'       => $resultCount,
                    'q'                 => '',
                ),
                $tpl_vars
            )
        );
    }

    /**
     * Get Solarium EntryPoint
     *
     * @return string
     */
    protected function entryPoint()
    {
        return 'solarium.client.matricules';
    }

    /**
     * Get map facets session name
     *
     * @return string
     */
    protected function mapFacetsName()
    {
        return 'matricules_map_facets';
    }

    /**
     * POST search destination for main form.
     *
     * Will take care of search terms, and reroute with proper URI
     *
     * @return void
     */
    public function doSearchAction()
    {
        $query = new SearchQuery();
        $form = $this->createForm(new SearchQueryFormType(), $query);
        $redirectUrl = $this->get('router')->generate('bach_matricules');

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $q = $query->getQuery();
                $redirectUrl = $this->get('router')->generate(
                    'bach_matricules',
                    array('query_terms' => $q)
                );

                $session = $this->getRequest()->getSession();
                $session->set('filters', null);
            }
        }
        return new RedirectResponse($redirectUrl);
    }
}
