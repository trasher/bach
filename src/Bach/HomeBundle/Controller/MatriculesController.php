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
        $tpl_vars = array();

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

        $viewer_uri = $this->container->getParameter('viewer_uri');
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

        if ( count($data) > 0 ) {
            $container = new SolariumQueryContainer();

            if ( $view_params->advancedSearch() ) {
                $container->setField('adv_matricules', $data);
            } else {
                $container->setField('matricules', $data['query']);
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

            if ( $show_maps ) {
                foreach ( $geoloc as $field ) {
                    $map_facets[$field] = $facetset->getFacet($field);
                }
            }

            $suggestions = $factory->getSuggestions(implode(' ', $data));
        } else {
            if ( $show_maps ) {
                $query = $this->get("solarium.client.matricules")->createSelect();
                $query->setQuery('*:*');
                $query->setStart(0)->setRows(0);

                $facetSet = $query->getFacetSet();
                $facetSet->setLimit(-1);
                $facetSet->setMinCount(1);
                foreach ( $geoloc as $field ) {
                    $facetSet->createFacetField($field)->setField($field);
                }

                $rs = $this->get('solarium.client.matricules')->select($query);

                foreach ( $geoloc as $field ) {
                    $map_facets[$field] = $rs->getFacetSet()->getFacet($field);
                }
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

        if ( $show_maps ) {
            $session->set('matricules_map_facets', $map_facets);
            $geojson = $factory->getGeoJson(
                $map_facets,
                $this->getDoctrine()
                    ->getRepository('BachIndexationBundle:Geoloc')
            );
            $tpl_vars['geojson'] = $geojson;
        }

        /*if ( $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Your comment has been stored. Thank you!')
            );
            return $this->redirect(
                $this->generateUrl(
                    'bach_display_document',
                    array(
                        'docid' => $docid
                    )
                )
            );
        } else {
            return $this->render(
                'BachHomeBundle:Comment:add.html.twig',
                array(
                    'docid'     => $docid,
                    'form'      => $form->createView(),
                    'eadfile'   => $eadfile
                )
            );
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
                    'show_maps'         => $show_maps,
                    'show_map'          => $view_params->showMap(),
                    'show_daterange'    => $view_params->showDaterange(),
                    'view'              => $view_params->getView(),
                    'results_order'     => $view_params->getOrder(),
                    'show_pics'         => $view_params->showPics(),
                    'q'                 => '',
                    'page'              => $page
                ),
                $tpl_vars
            )
        );
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
