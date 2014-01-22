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
    private $_geoloc;

    /**
     * Default page
     *
     * @return void
     */
    abstract public function indexAction();

    /**
     * Search page
     *
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     * @param string $facet_name  Display more terms in suggests
     *
     * @return void
     */
    abstract public function searchAction($query_terms = null, $page = 1,
        $facet_name = null
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
     *
     * @return void
     */
    protected function handleGeoloc(SolariumQueryFactory $factory, &$tpl_vars)
    {
        $show_maps = $this->container->getParameter('show_maps');
        if ( $show_maps ) {
            $request = $this->getRequest();
            $session = $request->getSession();
            $fields = $this->getGeolocFields();

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
     * Handle facets
     *
     * @param SolariumQueryFactory $factory       Query factory
     * @param array                $conf_facets   Configured facets
     * @param array                $searchResults Search results
     * @param Filters              $filters       Active filters
     * @param string               $facet_name    Facet name
     * @param array                &$tpl_vars     Template variables
     * @param array                $fields        Fields list
     *
     * @return void
     */
    protected function handleFacets(SolariumQueryFactory $factory, $conf_facets,
        $searchResults, $filters, $facet_name, &$tpl_vars, $fields = null
    ) {
        $request = $this->getRequest();
        $session = $request->getSession();

        $show_maps = $this->container->getParameter('show_maps');
        $facets = array();
        $facetset = $searchResults->getFacetSet();

        $facet_names = array(
            'geoloc'        => _('Map selection'),
            'date_begin'    => _('Start date'),
            'date_end'      => _('End date')
        );
        $facet_labels = array();

        foreach ( $conf_facets as $facet ) {
            $solr_field = $facet->getSolrFieldName();
            $facet_names[$solr_field] = $facet->getLabel($request->getLocale());
            $field_facets = $facetset->getFacet($solr_field);

            $values = array();
            foreach ( $field_facets as $item=>$count ) {
                if ( !$filters->offsetExists($solr_field)
                    || !$filters->hasValue($solr_field, $item)
                ) {
                    if ( in_array($solr_field, $this->getFacetsDateFields()) ) {
                        $start = null;
                        $end = null;

                        if ( strpos('|', $item) !== false ) {
                            list($start, $end) = explode('|', $item);
                        } else {
                            $start = $item;
                        }
                        $bdate = new \DateTime($start);

                        $edate = null;
                        if ( !$end ) {
                            $edate = new \DateTime($start);
                            $edate->add(
                                new \DateInterval(
                                    'P' . $factory->getDateGap()  . 'Y'
                                )
                            );
                            $edate->sub(new \DateInterval('PT1S'));
                        } else {
                            $edate = new \DateTime($end);
                        }
                        if ( !isset($facet_labels[$solr_field]) ) {
                            $facet_labels[$solr_field] = array();
                        }

                        $item = $bdate->format('Y-m-d\TH:i:s\Z') . '|' .
                            $edate->format('Y-m-d\TH:i:s\Z');

                        $ys = $bdate->format('Y');
                        $ye = $edate->format('Y');

                        if ( $ys != $ye ) {
                            $facet_labels[$solr_field][$item] = $ys . '-' . $ye;
                        } else {
                            $facet_labels[$solr_field][$item] = $ys;
                        }
                    }
                    $values[$item] = $count;
                }
            }

            if ( count($values) > 0 ) {
                if ( $facet->getSolrFieldName() !== 'dao' ) {
                    //facet order
                    $facet_order = $request->get('facet_order');
                    if ( !$facet_order || $facet_order == 0 ) {
                        arsort($values);
                    } else {
                        if ( defined('SORT_FLAG_CASE') ) {
                            //TODO: find a better way!
                            if ( $this->getRequest()->getLocale() == 'fr_FR' ) {
                                setlocale(LC_COLLATE, 'fr_FR.utf8');
                            }
                            ksort($values, SORT_LOCALE_STRING | SORT_FLAG_CASE);
                        } else {
                            //fallback for PHP < 5.4
                            ksort($values, SORT_LOCALE_STRING);
                        }
                    }
                }

                $do = true;
                if ( $facet->getSolrFieldName() === 'dao' ) {
                    foreach ( $values as $v ) {
                        if ( $v == 0 ) {
                            $do = false;
                        }
                    }
                }

                if ( in_array($facet->getSolrFieldName(), $this->getFacetsDateFields()) ) {
                    if ( count($values) == 1
                        && (in_array(1, $values)
                        || strpos('|', array_keys($values)[0]) === false)
                    ) {
                        $do = false;
                    }
                }

                if ( $do ) {
                    //get original URL if any
                    $tpl_vars['orig_href'] = $request->get('orig_href');
                    $tpl_vars['facet_order'] = $request->get('facet_order');

                    $facets[$facet->getSolrFieldName()] = array(
                        'label'         => $facet->getFrLabel(),
                        'content'       => $values,
                        'index_name'    => $facet->getSolrFieldName()
                    );
                }
            }
        }

        $browse_fields = $this->getDoctrine()
            ->getRepository('BachHomeBundle:BrowseFields')
            ->findBy(
                array('active' => true),
                array('position' => 'ASC')
            );
        foreach ( $browse_fields as $field ) {
            if ( !isset($facet_names[$field->getSolrFieldName()]) ) {
                $facet_names[$field->getSolrFieldName()]
                    = $field->getLabel($request->getLocale());
            }
        }

        if ( $show_maps ) {
            foreach ( $geoloc = $this->getGeolocFields() as $field ) {
                $map_facets[$field] = $facetset->getFacet($field);
            }
        }

        foreach ( $this->getFacetsDateFields() as $date_field ) {
            if ( $filters->offsetExists($date_field) ) {
                //set label for current date range filter
                if ( !isset($facet_labels[$date_field])) {
                    $facet_labels[$date_field] = array();
                }

                $cdate = $filters->offsetGet($date_field);
                list($start, $end) = explode('|', $cdate);
                $bdate = new \DateTime($start);
                $edate = new \DateTime($end);

                $ys = $bdate->format('Y');
                $ye = $edate->format('Y');

                if ( $ys != $ye ) {
                    $facet_labels[$date_field][$cdate] = $ys . '-' . $ye;
                } else {
                    $facet_labels[$date_field][$cdate] = $ys;
                }
            }
        }

        if ( count($facet_labels) > 0 ) {
            $tpl_vars['facet_labels'] = $facet_labels;
        }
        $tpl_vars['facet_names'] = $facet_names;

        $tpl_vars['facets'] = $facets;

        if ( $facet_name !== null ) {
            $tpl_vars['facet_name'] = $facet_name;
            $active = array_search($facet_name, array_keys($facets));
            if ( false !== $active ) {
                $tpl_vars['active_facet'] = $active;
            }
        }

        if ( $show_maps ) {
            $session->set('map_facets', $map_facets);
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
     * Get date fields
     *
     * @return array
     */
    abstract protected function getFacetsDateFields();

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
            $bbox,
            true
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
        $query = $this->get($this->entryPoint())->createSuggester();
        $query->setQuery(strtolower($this->getRequest()->get('q')));
        $query->setDictionary('suggest');
        $query->setOnlyMorePopular(true);
        $query->setCount(10);
        //$query->setCollate(true);
        $terms = $this->get($this->entryPoint())->suggester($query)->getResults();

        $suggestions = array();
        foreach ( $terms as $term ) {
            $suggestions = array_merge(
                $suggestions,
                $term->getSuggestions()
            );
        }

        return new JsonResponse($suggestions);
    }

    /**
     * List all entries for a specific facet
     *
     * @param string $query_terms Term(s) we search for
     * @param string $name        Facet name
     *
     * @return void
     */
    public function fullFacetAction($query_terms, $name)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $query_terms = urldecode($query_terms);

        $view_params = $session->get('view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        $view_params->bind($request);

        $geoloc = $this->getGeolocFields();

        $factory = $this->get("bach.home.solarium_query_factory");
        $factory->setGeolocFields($geoloc);

        $filters = $session->get('filters');
        if ( !$filters instanceof Filters ) {
            $filters = new Filters();
        }

        $conf_facets = $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array('active' => true),
                array('position' => 'ASC')
            );

        $container = new SolariumQueryContainer();
        $container->setOrder($view_params->getOrder());
        $container->setField('main', $query_terms);

        //Add filters to container
        $container->setFilters($filters);
        $factory->prepareQuery($container);

        $conf_facets = $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array(
                    'active'            => true,
                    'solr_field_name'   => $name
                )
            );

        $searchResults = $factory->performQuery(
            $container,
            $conf_facets
        );

        $facets = array();
        $facetset = $searchResults->getFacetSet();
        $current_facet = $facetset->getFacet($name);
        $facet = $conf_facets[0];
        $values = $current_facet->getValues();

        //facet order
        $facet_order = $request->get('facet_order');
        if ( !$facet_order || $facet_order == 0 ) {
            arsort($values);
        } else {
            if ( defined('SORT_FLAG_CASE') ) {
                ksort($values, SORT_FLAG_CASE | SORT_NATURAL);
            } else {
                //fallback for PHP < 5.4
                ksort($values, SORT_LOCALE_STRING);
            }
        }

        $facets[$facet->getSolrFieldName()] = array(
            'label'         => $facet->getFrLabel(),
            'content'       => $values,
            'index_name'    => $facet->getSolrFieldName()
        );

        $tpl_vars = array(
            'q'             => $query_terms,
            'facets'        => $facets,
            'orig_href'     => $request->get('orig_href'),
            'facet_order'   => $request->get('facet_order')
        );

        return $this->render(
            'BachHomeBundle:Default:facet.html.twig',
            $tpl_vars
        );
    }

    /**
     * Get configured geolocalization fields
     *
     * @return array
     */
    protected function getGeolocFields()
    {
        $show_maps = $this->container->getParameter('show_maps');
        if ( $show_maps && !isset($this->_geoloc) ) {
            $class = $this->getGeolocClass();
            $gf = new $class;
            $gf = $gf->loadDefaults(
                $this->getDoctrine()->getManager()
            );
            $this->_geoloc = $gf->getSolrFieldsNames();
        }
        return $this->_geoloc;
    }

    /**
     * Get golocalization fields class name
     *
     * @return string
     */
    abstract protected function getGeolocClass();

    /**
     * Handle yearly results
     *
     * @param SolariumQueryFactory $factory   Factory instance
     * @param array                &$tpl_vars Template variables
     *
     * @return void
     */
    protected function handleYearlyResults($factory, &$tpl_vars)
    {
        $by_year = $factory->getResultsByYear();
        if ( count($by_year) > 0 ) {
            $tpl_vars['by_year'] = $by_year;
            $date_min = new \DateTime($by_year[0][0] . '-01-01');
            $date_max = new \DateTime($by_year[count($by_year)-1][0] . '-01-01');
            $tpl_vars['by_year_min'] = (int)$date_min->format('Y');
            $tpl_vars['by_year_max'] = (int)$date_max->format('Y');
        }
    }
}
