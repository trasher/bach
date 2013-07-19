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
use Bach\HomeBundle\Entity\SearchQueryFormType;
use Bach\HomeBundle\Entity\SearchQuery;
use Bach\HomeBundle\Entity\Sidebar\OptionSidebarItemChoice;
use Bach\HomeBundle\Builder\OptionSidebarBuilder;
use Bach\HomeBundle\Entity\Sidebar\OptionSidebarItem;
use Bach\HomeBundle\Entity\Sidebar\OptionSidebar;
use Bach\HomeBundle\Entity\SearchForm;
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
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     *
     * @return void
     */
    public function indexAction($query_terms = null, $page = 1)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ( $query_terms !== null ) {
            $query_terms = urldecode($query_terms);
        }

        //instanciate - if needed - sidebar values
        $resultByPage = $session->get('results_by_page');
        if ( !$resultByPage ) {
            $resultByPage = 10;
        }

        $showPics = $session->get('show_pics');
        if ( !isset($showPics) ) {
            $showPics = 1;
        }

        // Construction de la barre de gauche comprenant les options de recherche
        $sidebar = new OptionSidebar();

        $resultsItem = new OptionSidebarItem(
            "Nombre de résultats par page",
            "qo_pr",
            $resultByPage
        );
        $resultsItem
            ->appendChoice(new OptionSidebarItemChoice("10", 10))
            ->appendChoice(new OptionSidebarItemChoice("20", 20))
            ->appendChoice(new OptionSidebarItemChoice("50", 50));
        $sidebar->append($resultsItem);

        $picturesItem = new OptionSidebarItem(
            "Afficher les images",
            'show_pics',
            $showPics
        );
        $picturesItem
            ->appendChoice(new OptionSidebarItemChoice("Oui", 1))
            ->appendChoice(new OptionSidebarItemChoice("Non", 0));
        $sidebar->append($picturesItem);

        $sidebar->bind(
            $request,
            $this->get('router')->generate(
                'bach_search',
                array(
                    'query_terms'   => urlencode($query_terms),
                    'page'          => $page
                )
            )
        );

        $builder = new OptionSidebarBuilder($sidebar);
        $templateVars = array(
            'q'             => urlencode($query_terms),
            'page'          => $page,
            'sidebar'       => $builder->compileToArray(),
            'show_pics'     => $sidebar->getItemValue('show_pics')
        );

        if ( !is_null($query_terms) ) {
            // On effectue une recherche
            $form = $this->createForm(
                new SearchQueryFormType($query_terms),
                new SearchQuery()
            );

            $container = new SolariumQueryContainer();
            $container->setField(
                'show_pics',
                $sidebar->getItemValue('show_pics')
            );
            $container->setField("main", $query_terms);

            $resultByPage = intval($sidebar->getItemValue("qo_pr"));

            $session->set('results_by_page', $resultByPage);
            $session->set('show_pics', $sidebar->getItemValue('show_pics'));

            $container->setField(
                "pager",
                array(
                    "start"     => ($page - 1) * $resultByPage,
                    "offset"    => $resultByPage
                )
            );

            $filters = $session->get('filters');
            if ( !is_array($filters) ) {
                $filters = array();
            }

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
                    break;
                default:
                    if ( isset($filters[$filter_field])
                        && is_array($filters[$filter_field])
                        && !in_array($filter_value[0], $filters[$filter_field])
                    ) {
                        $filter_value = array_push(
                            $filters[$filter_field],
                            $filter_value[0]
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

            $factory = $this->get("bach.home.solarium_query_factory");
            $searchResults = $factory->performQuery($container);
            $hlSearchResults = $factory->getHighlighting();
            $scSearchResults = $factory->getSpellcheck();
            $resultCount = $searchResults->getNumFound();

            $facets = array();
            $faceset = $searchResults->getFacetSet();
            $facets['subject'] = $faceset->getFacet('subject');
            $facets['persname'] = $faceset->getFacet('persname');
            $facets['geogname'] = $faceset->getFacet('geogname');

            $query = $this->get("solarium.client")->createSuggester();
            $query->setQuery(strtolower($query_terms));
            $query->setDictionary('suggest');
            $query->setOnlyMorePopular(true);
            $query->setCount(10);
            //$query->setCollate(true);
            $suggestions = $this->get("solarium.client")->suggester($query);

            $templateVars['resultCount'] = $resultCount;
            $templateVars['resultByPage'] = $resultByPage;
            $templateVars['totalPages'] = ceil($resultCount/$resultByPage);
            $templateVars['searchResults'] = $searchResults;
            $templateVars['hlSearchResults'] = $hlSearchResults;
            $templateVars['scSearchResults'] = $scSearchResults;
            $templateVars['facets'] = $facets;

            $templateVars['resultStart'] = ($page - 1) * $resultByPage + 1;
            $resultEnd = ($page - 1) * $resultByPage + $resultByPage;
            if ( $resultEnd > $resultCount ) {
                $resultEnd = $resultCount;
            }
            $templateVars['resultEnd'] = $resultEnd;

        } else {
            $form = $this->createForm(new SearchQueryFormType(), new SearchQuery());

            $query = $this->get("solarium.client")->createSelect();
            $query->setQuery('*:*');
            $query->setStart(0)->setRows(0);

            $facetSet = $query->getFacetSet();
            $facetSet->setLimit(100);
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
                $min = $values[100];
                //10 levels
                $range = ($max - $min) / 9;

                $tagcloud = array();
                $i = 0;
                //loop through returned result and normalize keyword hit counts
                foreach ( $tags as $keyword=>$weight ) {
                    if ( $i === 100 ) {
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
        /*if ( $current_query === null ) {
            $current_query = $this->get('solarium.client')->createSelect();
            $current_query->setQuery('*:*');
        }
        $current_query->setRows(0);
        $stats = $current_query->getStats();*/

        $query = $this->get('solarium.client')->createSelect();
        $query->setQuery('*:*');
        $query->setRows(0);
        $stats = $query->getStats();

        $stats->createField('cDateBegin');
        $stats->createField('cDateEnd');
        $rs = $this->get('solarium.client')->select($query);
        /*$rs = $this->get('solarium.client')->select($current_query);*/
        $rsStats = $rs->getStats();
        $statsResults = $rsStats->getResults();

        $min_date = $statsResults['cDateBegin']->getMin();
        $dates_count = $statsResults['cDateBegin']->getCount();
        $max_date = $statsResults['cDateEnd']->getMax();

        if ( $min_date && $max_date ) {
            $templateVars['min_date'] = explode('-', $min_date);
            if ( isset($filters['cDateBegin']) ) {
                $templateVars['selected_min_date'] = explode(
                    '-',
                    $filters['cDateBegin'][0]
                );
            } else {
                $templateVars['selected_min_date'] = $templateVars['min_date'];
            }
            $templateVars['max_date'] = explode('-', $max_date);
            if ( isset($filters['cDateEnd']) ) {
                $templateVars['selected_max_date'] = explode(
                    '-',
                    $filters['cDateEnd'][0]
                );
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
     *
     * @return void
     */
    public function browseAction($part = null, $show_all = false)
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
        }
        $query->setLimit($limit);
        //$query->setLowerbound('i');

        $query->setFields('cSubject,cPersname,cGeogname');

        $found_terms = $client->terms($query);
        foreach ( $found_terms as $field=>$terms ) {
            $lists[$field] = array();
            foreach ( $terms as $term=>$count ) {
                $lists[$field][] = $term . ' (' . $count . ')';
            }
        }

        $templateVars['lists'] = $lists;

        return $this->render(
            'BachHomeBundle:Default:browse.html.twig',
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
     * @param int $docid Document unique identifier
     *
     * @return void
     */
    public function displayDocumentAction($docid)
    {
        $client = $this->get("solarium.client");
        $query = $client->createSelect();
        $query->setQuery('uniqid:' . $docid);
        $query->setFields('fragment');
        $query->setStart(0)->setRows(1);

        $rs = $client->select($query);

        if ( $rs->getNumFound() !== 1 ) {
            throw new \RuntimeException(
                $rs->getNumFound() . ' results found, 1 excpected.'
            );
        }

        $request = $client->createRequest($query);
        $uri = $request->getUri();
        
        $docs  = $rs->getDocuments();
        $doc = $docs[0];
        return $this->render(
            'BachHomeBundle:Default:display.html.twig',
            array(
                'docid'     => $docid,
                'document'  => $doc
            )
        );

        echo $docid;
    }
}
