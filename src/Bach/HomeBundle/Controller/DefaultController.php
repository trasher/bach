<?php
/**
 * Bach home controller
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
use Bach\HomeBundle\Entity\GeolocFields;

/**
 * Bach home controller
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class DefaultController extends SearchController
{
    /**
     * Default page
     *
     * @return void
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        /** Manage view parameters */
        $view_params = $session->get('view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        //take care of user view params
        if ( isset($_COOKIE['bach_view_params']) ) {
            $view_params->bindCookie('bach_view_params');
        }

        //set current view parameters according to request
        $view_params->bind($request);

        $tpl_vars = $this->searchTemplateVariables($view_params);

        $form = $this->createForm(
            new SearchQueryFormType(),
            new SearchQuery()
        );
        $tpl_vars['form'] = $form->createView();

        $factory = $this->get("bach.home.solarium_query_factory");
        $factory->setDateField('cDateBegin');

        $show_tagcloud = $this->container->getParameter('show_tagcloud');
        if ( $show_tagcloud ) {
            $tagcloud = $factory->getTagCloud($this->getDoctrine()->getManager());

            if ( $tagcloud ) {
                $tpl_vars['tagcloud'] = $tagcloud;
            }
        }

        $this->handleGeoloc($factory, $tpl_vars);

        $slider_dates = $factory->getSliderDates(new Filters());
        if ( is_array($slider_dates) ) {
            $tpl_vars = array_merge($tpl_vars, $slider_dates);
        }
        $this->handleYearlyResults($factory, $tpl_vars);

        return $this->render(
            'BachHomeBundle:Default:index.html.twig',
            $tpl_vars
        );
    }

    /**
     * Get Solarium EntryPoint
     *
     * @return string
     */
    protected function entryPoint()
    {
        return 'solarium.client';
    }

    /**
     * Get map facets session name
     *
     * @return string
     */
    protected function mapFacetsName()
    {
        return 'map_facets';
    }

    /**
     * Get date fields
     *
     * @return array
     */
    protected function getFacetsDateFields()
    {
        return array('cDate');
    }

    /**
     * Get golocalization fields class name
     *
     * @return string
     */
    protected function getGeolocClass()
    {
        return 'Bach\HomeBundle\Entity\GeolocMainFields';
    }

    /**
     * Search page
     *
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     * @param string $facet_name  Display more terms in suggests
     *
     * @return void
     */
    public function searchAction($query_terms = null, $page = 1,
        $facet_name = null
    ) {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ( $query_terms !== null ) {
            $query_terms = urldecode($query_terms);
        }

        /** Manage view parameters */
        $view_params = $session->get('view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        //take care of user view params
        if ( isset($_COOKIE['bach_view_params']) ) {
            $view_params->bindCookie('bach_view_params');
        }

        //set current view parameters according to request
        $view_params->bind($request);

        //store new view parameters
        $session->set('view_params', $view_params);

        $show_maps = $this->container->getParameter('show_maps');

        $filters = $session->get('filters');
        if ( !$filters instanceof Filters || $request->get('clear_filters') ) {
            $filters = new Filters();
            $session->set('filters', null);
        }

        $filters->bind($request);
        $session->set('filters', $filters);

        if ( ($request->get('filter_field') || $filters->count() > 0)
            && is_null($query_terms)
        ) {
            $query_terms = '*:*';
        } else if ( $query_terms === null && $filters->count() == 0 ) {
            $redirectUrl = $this->get('router')->generate('bach_homepage');
            return new RedirectResponse($redirectUrl);
        }

        $templateVars = $this->searchTemplateVariables($view_params, $page);
        $templateVars = array_merge(
            $templateVars,
            array(
                'q'             => urlencode($query_terms),
                'show_pics'     => $view_params->showPics(),
                'show_map'      => $view_params->showMap(),
                'show_daterange'=> $view_params->showDaterange(),
                'view'          => $view_params->getView(),
                'results_order' => $view_params->getOrder()
            )
        );

        $factory = $this->get("bach.home.solarium_query_factory");
        $factory->setGeolocFields($this->getGeolocFields());
        $factory->setDateField('cDateBegin');

        $map_facets = array();

        // On effectue une recherche
        $form = $this->createForm(
            new SearchQueryFormType($query_terms),
            new SearchQuery()
        );

        $container = new SolariumQueryContainer();
        $container->setOrder($view_params->getOrder());

        $container->setField(
            'show_pics',
            $view_params->showPics()
        );
        $container->setField("main", $query_terms);

        $container->setField(
            "pager",
            array(
                "start"     => ($page - 1) * $view_params->getResultsbyPage(),
                "offset"    => $view_params->getResultsbyPage()
            )
        );

        //Add filters to container
        $container->setFilters($filters);
        if ( $filters->count() > 0 ) {
            $templateVars['filters'] = $filters;
        }

        $factory->prepareQuery($container);

        $conf_facets = $this->getDoctrine()
            ->getRepository('BachHomeBundle:Facets')
            ->findBy(
                array('active' => true),
                array('position' => 'ASC')
            );

        $searchResults = $factory->performQuery(
            $container,
            $conf_facets
        );

        $hlSearchResults = $factory->getHighlighting();
        $scSearchResults = $factory->getSpellcheck();
        $resultCount = $searchResults->getNumFound();

        $this->handleFacets(
            $factory,
            $conf_facets,
            $searchResults,
            $filters,
            $facet_name,
            $templateVars
        );
        $suggestions = $factory->getSuggestions($query_terms);

        $templateVars['resultCount'] = $resultCount;
        $templateVars['resultByPage'] = $view_params->getResultsbyPage();
        $templateVars['totalPages'] = ceil(
            $resultCount/$view_params->getResultsbyPage()
        );
        $templateVars['searchResults'] = $searchResults;
        $templateVars['hlSearchResults'] = $hlSearchResults;
        $templateVars['scSearchResults'] = $scSearchResults;
        $templateVars['resultStart'] = ($page - 1)
            * $view_params->getResultsbyPage() + 1;
        $resultEnd = ($page - 1) * $view_params->getResultsbyPage()
            + $view_params->getResultsbyPage();
        if ( $resultEnd > $resultCount ) {
            $resultEnd = $resultCount;
        }
        $templateVars['resultEnd'] = $resultEnd;

        $slider_dates = $factory->getSliderDates($filters);
        if ( is_array($slider_dates) ) {
            $templateVars = array_merge($templateVars, $slider_dates);
        }

        $this->handleYearlyResults($factory, $templateVars);

        $templateVars['form'] = $form->createView();

        if ( isset($suggestions) && $suggestions->count() > 0 ) {
            $templateVars['suggestions'] = $suggestions;
        }

        return $this->render(
            'BachHomeBundle:Default:index.html.twig',
            $templateVars
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
        $redirectUrl = $this->get('router')->generate('bach_homepage');

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $q = $query->getQuery();
                $redirectUrl = $this->get('router')->generate(
                    'bach_search',
                    array('query_terms' => $q)
                );

                $session = $this->getRequest()->getSession();
                $session->set('filters', null);
            }
        }
        return new RedirectResponse($redirectUrl);
    }

    /**
     * Browse contents
     *
     * @param string  $part     Part to browse
     * @param boolean $show_all Show all results
     * @param boolean $ajax     If we were called from ajax
     *
     * @return void
     */
    public function browseAction($part = '', $show_all = false, $ajax = false)
    {
        $fields = $this->getDoctrine()
            ->getRepository('BachHomeBundle:BrowseFields')
            ->findBy(
                array('active' => true),
                array('position' => 'ASC')
            );

        $field = null;
        if ( $part === '' && count($fields) >0 ) {
            $field = $fields[0];
            $part = $field->getSolrFieldName();
        } else if ( count($fields) > 0 ) {
            foreach ( $fields as $f ) {
                if ( $f->getSolrFieldName() === $part ) {
                    $field = $f;
                    break;
                }
            }
        }

        $templateVars = array(
            'fields'        => $fields,
            'current_field' => $field,
            'part'          => $part
        );

        $lists = array();

        $limit = 20;
        if ( $show_all === 'show_all' ) {
            $limit = -1;
            $templateVars['show_all'] = true;
        } else {
            $templateVars['show_all'] = 'false';
        }

        if ( $part !== '' ) {
            $client = $this->get($this->entryPoint());
            // get a terms query instance
            $query = $client->createTerms();

            $query->setLimit($limit);

            $query->setFields($part);

            $found_terms = $client->terms($query);
            foreach ( $found_terms as $field=>$terms ) {
                $lists[$field] = array();
                $current_values = array();
                foreach ( $terms as $term=>$count ) {
                    $current_values[$term] = array(
                        'term'  => $term,
                        'count' => $count
                    );
                }
                if ( $show_all === 'show_all' ) {
                    if ( defined('SORT_FLAG_CASE') ) {
                        //TODO: find a better way!
                        if ( $this->getRequest()->getLocale() == 'fr_FR' ) {
                            setlocale(LC_COLLATE, 'fr_FR.utf8');
                        }
                        ksort($current_values, SORT_LOCALE_STRING | SORT_FLAG_CASE);
                    } else {
                        //fallback for PHP < 5.4
                        ksort($current_values, SORT_LOCALE_STRING);
                    }
                }
                $lists[$field] = $current_values;
            }
        }

        $templateVars['lists'] = $lists;

        if ( $ajax === false ) {
            $tpl_name = 'browse';
        } else {
            $tpl_name = 'browse_tab_contents';
        }

        return $this->render(
            'BachHomeBundle:Default:' . $tpl_name  . '.html.twig',
            $templateVars
        );
    }

    /**
     * Document display
     *
     * @param int     $docid Document unique identifier
     * @param int     $page  Page
     * @param boolean $ajax  Called from ajax
     *
     * @return void
     */
    public function displayDocumentAction($docid, $page = 1, $ajax = false)
    {
        $client = $this->get($this->entryPoint());
        $query = $client->createSelect();
        $query->setQuery('fragmentid:"' . $docid . '"');
        $query->setFields(
            'headerId, fragment, parents, archDescUnitTitle, cUnittitle, cDate'
        );
        $query->setStart(0)->setRows(1);

        $rs = $client->select($query);

        if ( $rs->getNumFound() !== 1 ) {
            throw new \RuntimeException(
                str_replace(
                    '%count%',
                    $rs->getNumFound(),
                    _('%count% results found, 1 expected.')
                )
            );
        }

        $docs  = $rs->getDocuments();
        $doc = $docs[0];

        $tpl = '';

        $tplParams = $this->commonTemplateVariables();
        $tplParams = array_merge(
            $tplParams,
            array(
                'docid'         => $docid,
                'document'      => $doc,
                'archdesc'      => $doc['archDescUnitTitle']
            )
        );

        $parents = explode('/', $doc['parents']);
        if ( count($parents) > 0 ) {
            $pquery = $client->createSelect();
            $query = null;
            foreach ( $parents as $p ) {
                if ( $query !== null ) {
                    $query .= ' | ';
                }
                $query .= 'fragmentid:"' . $doc['headerId'] . '_' . $p . '"';
            }
            $pquery->setQuery($query);
            $pquery->setFields('fragmentid, cUnittitle');
            $rs = $client->select($pquery);
            $ariane  = $rs->getDocuments();
            if ( count($ariane) > 0 ) {
                $tplParams['ariane'] = $ariane;
            }
        }

        $max_results = 20;
        $cquery = $client->createSelect();
        $pid = substr($docid, strlen($doc['headerId']) + 1);

        $query = '+headerId:"' . $doc['headerId'] . '" +parents: ';
        if ( $pid === 'description' ) {
            $query .= '""';
        } else {
            if ( isset($doc['parents']) && trim($doc['parents'] !== '') ) {
                $pid = $doc['parents'] . '/' . $pid;
            }
            $query .= $pid;
        }
        $cquery->setQuery($query);
        $cquery->setStart(($page - 1) * $max_results);
        $cquery->setRows($max_results);
        $cquery->setFields('fragmentid, cUnittitle');
        $rs = $client->select($cquery);
        $children  = $rs->getDocuments();
        $count_children = $rs->getNumFound();

        $tplParams['count_children'] = $count_children;
        if ( count($children) > 0 ) {
            $tplParams['children'] = $children;
            if ( count($children) < $count_children ) {
                $tplParams['totalPages'] = ceil($count_children/$max_results);
                $tplParams['page'] = $page;
            }
        } else {
            $tplParams['children'] = false;
        }

        if ( $ajax === 'ajax' ) {
            $tpl = 'BachHomeBundle:Default:content_display.html.twig';
            $tplParams['ajax'] = true;
        } else {
            $tpl = 'BachHomeBundle:Default:display.html.twig';
            $tplParams['ajax'] = false;
        }

        //retrieve comments
        $query = $this->getDoctrine()->getManager()
            ->createQuery(
                'SELECT c, d FROM BachHomeBundle:Comment c
                JOIN c.eadfile d
                WHERE c.state = :state
                AND d.fragmentid = :docid
                ORDER BY c.creation_date DESC, c.id DESC'
            )->setParameters(
                array(
                    'state' => Comment::PUBLISHED,
                    'docid' => $docid
                )
            );
        $comments = $query->getResult();
        if ( count($comments) > 0 ) {
            $tplParams['comments'] = $comments;
        }

        /** FIXME: find a suitable comportement for the stuff to avoid loops
        $referer = $this->getRequest()->headers->get('referer');
        if ( $referer !== null ) {
            $tplParams['referer'] = $referer;
        }*/

        return $this->render(
            $tpl,
            $tplParams
        );
    }

    /**
     * Display classification scheme
     *
     * @return void
     */
    public function cdcAction()
    {
        $tplParams = array();

        $client = $this->get($this->entryPoint());
        $query = $client->createSelect();
        $query->setQuery('fragmentid:*_description');
        $query->setFields('cUnittitle, headerId, fragmentid');
        $query->setStart(0)->setRows(1000);

        $rs = $client->select($query);

        $published = new \SimpleXMLElement(
            '<docs></docs>'
        );

        foreach ( $rs as $doc ) {
            $published->addChild($doc->headerId, $doc->cUnittitle);
        }

        $tplParams['docs'] = $published;

        return $this->render(
            'BachHomeBundle:Default:cdc.html.twig',
            $tplParams
        );
    }
}
