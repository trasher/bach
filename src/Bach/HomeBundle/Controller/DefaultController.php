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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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
class DefaultController extends Controller
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
        $view_params = $session->get('view_params');
        if ( !$view_params ) {
            $view_params = new ViewParams();
        }
        //set current view parameters according to request
        $view_params->bind($request);
        //store new view parameters
        $session->set('view_params', $view_params);

        $filters = $session->get('filters');
        if ( !is_array($filters) ) {
            $filters = array();
        }

        if ( ($request->get('filter_field') || count($filters) > 0)
            && is_null($query_terms)
        ) {
            $query_terms = '*:*';
        }

        $viewer_uri = $this->container->getParameter('viewer_uri');

        $templateVars = array(
            'q'             => urlencode($query_terms),
            'page'          => $page,
            'show_pics'     => $view_params->showPics(),
            'viewer_uri'    => $viewer_uri,
            'view'          => $view_params->getView(),
            'results_order' => $view_params->getOrder(),
            'illustrated'   => $view_params->getIllustrated()
        );

        if ( $facet_name !== null ) {
            $templateVars['facet_name'] = $facet_name;
        }

        if ( !is_null($query_terms) ) {
            // On effectue une recherche
            $form = $this->createForm(
                new SearchQueryFormType($query_terms),
                new SearchQuery()
            );

            $container = new SolariumQueryContainer();
            $container->setOrder($view_params->getOrder());
            $container->isIllustrated($view_params->getIllustrated());

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

            if ( $request->get('clear_filters') ) {
                $filters = array();
                $session->set('filters', null);
            }

            if ( $request->get('rm_filter_field') ) {
                $rm_filter_field = $request->get('rm_filter_field');
                $rm_filter_value = $request->get('rm_filter_value');

                switch ( $rm_filter_field ) {
                case 'cDateBegin':
                case 'cDateEnd':
                    unset($filters[$rm_filter_field]);
                    break;
                default:
                    if ( isset($filters[$rm_filter_field]) ) {
                        $values = &$filters[$rm_filter_field];
                        foreach ( $values as $k=>$v ) {
                            if ( $v == $rm_filter_value ) {
                                unset ($values[$k]);
                            }
                        }
                        if ( count($values) == 0 ) {
                            unset($filters[$rm_filter_field]);
                        }
                    }
                }

                $session->set('filters', $filters);
            }

            if ( $request->get('filter_field') ) {

                $filter_field = $request->get('filter_field');
                $filter_value = array($request->get('filter_value'));

                switch ( $filter_field ) {
                case 'cDateBegin':
                case 'cDateEnd':
                    $php_date = \DateTime::createFromFormat('Y', $filter_value[0]);
                    if ( $filter_field === 'cDateBegin' ) {
                        $filter_value = array($php_date->format('Y-01-01'));
                    } else {
                        $filter_value = array($php_date->format('Y-12-31'));
                    }
                    break;
                default:
                    if ( isset($filters[$filter_field])
                        && is_array($filters[$filter_field])
                        && !in_array($filter_value[0], $filters[$filter_field])
                    ) {
                        $new_value = $filter_value[0];
                        $filter_value = $filters[$filter_field];
                        array_push(
                            $filter_value,
                            $new_value
                        );
                    }
                }
                $filters[$filter_field] = $filter_value;
                $session->set('filters', $filters);
            }

            //Add filters to container
            $container->setFilters($filters);
            if ( count($filters) > 0 ) {
                $templateVars['filters'] = $filters;
            }

            $conf_facets = $this->getDoctrine()->getRepository('BachHomeBundle:Facets')->findBy(
                array('active' => true),
                array('position' => 'ASC')
            );

            $factory = $this->get("bach.home.solarium_query_factory");
            $searchResults = $factory->performQuery($container, $conf_facets);
            $hlSearchResults = $factory->getHighlighting();
            $scSearchResults = $factory->getSpellcheck();
            $resultCount = $searchResults->getNumFound();

            $facets = array();
            $facetset = $searchResults->getFacetSet();

            if ( $ajax !== false && $facet_name !== false ) {
                foreach ( $conf_facets as $facet ) {
                    if ( $facet->getSolrFieldName() === $facet_name ) {
                        $conf_facets = array($facet);
                        break;
                    }
                }
            }

            $facet_names = array(
                'cDateBegin'    => _('Start date'),
                'cDateEnd'      => _('End date')
            );
            foreach ( $conf_facets as $facet ) {
                $facet_names[$facet->getSolrFieldName()] = $facet->getFrLabel();
                $field_facets = $facetset->getFacet($facet->getSolrFieldName());
                $values = array();
                foreach ( $field_facets as $item=>$count ) {
                    if ( !isset($filters[$facet->getSolrFieldName()])
                        || !in_array($item, $filters[$facet->getSolrFieldName()])
                    ) {
                        $values[$item] = $count;
                    }
                }
                if ( count($values) > 0 ) {
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

                    //get original URL if any
                    $templateVars['orig_href'] = $request->get('orig_href');
                    $templateVars['facet_order'] = $request->get('facet_order');

                    $facets[$facet->getSolrFieldName()] = array(
                        'label'         => $facet->getFrLabel(),
                        'content'       => $values,
                        'index_name'    => $facet->getSolrFieldName()
                    );
                }
            }
            $templateVars['facet_names'] = $facet_names;

            if ( $ajax === false ) {
                $query = $this->get("solarium.client")->createSuggester();
                $query->setQuery(strtolower($query_terms));
                $query->setDictionary('suggest');
                $query->setOnlyMorePopular(true);
                $query->setCount(10);
                //$query->setCollate(true);
                $suggestions = $this->get("solarium.client")->suggester($query);


                $templateVars['resultCount'] = $resultCount;
                $templateVars['resultByPage'] = $view_params->getResultsbyPage();
                $templateVars['totalPages'] = ceil($resultCount/$view_params->getResultsbyPage());
                $templateVars['searchResults'] = $searchResults;
                $templateVars['hlSearchResults'] = $hlSearchResults;
                $templateVars['scSearchResults'] = $scSearchResults;
            }
            $templateVars['facets'] = $facets;

            if ( $ajax === false ) {
                $templateVars['resultStart'] = ($page - 1) * $view_params->getResultsbyPage() + 1;
                $resultEnd = ($page - 1) * $view_params->getResultsbyPage() + $view_params->getResultsbyPage();
                if ( $resultEnd > $resultCount ) {
                    $resultEnd = $resultCount;
                }
                $templateVars['resultEnd'] = $resultEnd;
            }
        } else {
            $tag_max = 20;

            $form = $this->createForm(new SearchQueryFormType(), new SearchQuery());

            $query = $this->get("solarium.client")->createSelect();
            $query->setQuery('*:*');
            $query->setStart(0)->setRows(0);

            $facetSet = $query->getFacetSet();
            $facetSet->setLimit($tag_max);
            $facetSet->setMinCount(1);
            $facetSet->createFacetField('subject')->setField('cSubject');
            $facetSet->createFacetField('persname')->setField('cPersname');
            $facetSet->createFacetField('geogname')->setField('cGeogname');

            $rs = $this->get('solarium.client')->select($query);

            $tags = array();
            $facet = $rs->getFacetSet()->getFacet('subject');
            $tags = $facet->getValues();

            $facet = $rs->getFacetSet()->getFacet('persname');
            $tags = array_merge($tags, $facet->getValues());

            $facet = $rs->getFacetSet()->getFacet('geogname');
            $tags = array_merge($tags, $facet->getValues());

            if ( count($tags) > 0 ) {
                arsort($tags, SORT_NUMERIC);

                $values = array_values($tags);
                $max = $values[0];
                $min = null;
                if ( count($values) < $tag_max ) {
                    $min = $values[count($values)-1];
                } else {
                    $min = $values[$tag_max];
                }

                //5 levels
                $range = ($max - $min) / 5;

                $tagcloud = array();
                $i = 0;
                //loop through returned result and normalize keyword hit counts
                foreach ( $tags as $keyword=>$weight ) {
                    if ( $i === $tag_max ) {
                        break;
                    }

                    $tagcloud[$keyword] = floor($weight/$range);
                    $i++;
                }

                ksort($tagcloud, SORT_LOCALE_STRING);
                $templateVars['tagcloud'] = $tagcloud;
            }
        }

        //get min and max dates
        if ( $ajax === false ) {
            $query = $this->get('solarium.client')->createSelect();
            $query->setQuery('*:*');
            $query->setRows(0);
            $stats = $query->getStats();

            $stats->createField('cDateBegin');
            $stats->createField('cDateEnd');
            $rs = $this->get('solarium.client')->select($query);
            $rsStats = $rs->getStats();
            $statsResults = $rsStats->getResults();

            $min_date = $statsResults['cDateBegin']->getMin();
            $max_date = $statsResults['cDateEnd']->getMax();

            if ( $min_date && $max_date ) {
                $step_unit = 'years';
                $step = 1;

                $php_min_date = new \DateTime($min_date);
                $php_max_date = new \DateTime($max_date);

                $diff = $php_min_date->diff($php_max_date);
                if ( $diff->y > 100 ) {
                    $step = $diff->y / 100;
                }

                $templateVars['date_step_unit'] = $step_unit;
                $templateVars['date_step'] = $step;

                $templateVars['min_date'] = $php_min_date->format('Y');
                if ( isset($filters['cDateBegin']) ) {
                    $dbegin = explode(
                        '-',
                        $filters['cDateBegin'][0]
                    );
                    $templateVars['selected_min_date'] = $dbegin[0];
                } else {
                    $templateVars['selected_min_date'] = $templateVars['min_date'];
                }
                $templateVars['max_date'] = $php_max_date->format('Y');
                if ( isset($filters['cDateEnd']) ) {
                    $dend = explode(
                        '-',
                        $filters['cDateEnd'][0]
                    );
                    $templateVars['selected_max_date'] = $dend[0];
                } else {
                    $templateVars['selected_max_date'] = $templateVars['max_date'];
                }
            }

            $templateVars['form'] = $form->createView();
            if ( $this->container->get('kernel')->getEnvironment() == 'dev'
                && isset($factory)
            ) {
                //let's pass Solr raw query to template
                $templateVars['solr_qry'] = $factory->getRequest()->getUri();
            }

            if ( isset($suggestions) && $suggestions->count() > 0 ) {
                $templateVars['suggestions'] = $suggestions;
            }

            return $this->render(
                'BachHomeBundle:Default:index.html.twig',
                $templateVars
            );
        } else {
            return $this->render(
                'BachHomeBundle:Default:facet.html.twig',
                $templateVars
            );
        }
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
    public function browseAction($part, $show_all = false, $ajax = false)
    {
        $templateVars = array(
            'part'          => $part
        );

        $lists = array();

        $client = $this->get("solarium.client");
        // get a terms query instance
        $query = $client->createTerms();

        $limit = 20;
        if ( $show_all === 'show_all' ) {
            $limit = -1;
            $templateVars['show_all'] = true;
        } else {
            $templateVars['show_all'] = 'false';
        }
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
                    ksort($current_values, SORT_FLAG_CASE | SORT_NATURAL);
                } else {
                    //fallback for PHP < 5.4
                    ksort($current_values, SORT_LOCALE_STRING);
                }
            }
            $lists[$field] = $current_values;
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

    /**
     * Document display
     *
     * @param int     $docid Document unique identifier
     * @param boolean $ajax  Called from ajax
     *
     * @return void
     */
    public function displayDocumentAction($docid, $ajax = false)
    {
        $client = $this->get("solarium.client");
        $query = $client->createSelect();
        $query->setQuery('fragmentid:"' . $docid . '"');
        $query->setFields('headerId, fragment, parents, archDescUnitTitle');
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

        $viewer_uri = $this->container->getParameter('viewer_uri');

        $docs  = $rs->getDocuments();
        $doc = $docs[0];

        $tpl = '';

        $tplParams = array(
            'docid'         => $docid,
            'document'      => $doc,
            'viewer_uri'    => $viewer_uri,
            'archdesc'      => $doc['archDescUnitTitle']
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

        $cquery = $client->createSelect();
        $pid = substr($docid, strlen($doc['headerId']) + 1);
        if ( isset($doc['parents']) && trim($doc['parents'] !== '') ) {
            $pid = $doc['parents'] . '/' . $pid;
        }
        $query = '+headerId:"' . $doc['headerId'] . '" +parents: ' . $pid;
        $cquery->setQuery($query);
        $cquery->setFields('fragmentid, cUnittitle');
        $rs = $client->select($cquery);
        $children  = $rs->getDocuments();

        if ( count($children) > 0 ) {
            $tplParams['children'] = $children;
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
}
