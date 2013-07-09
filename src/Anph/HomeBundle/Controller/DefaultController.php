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

namespace Anph\HomeBundle\Controller;

use Anph\HomeBundle\Entity\SolariumQueryContainer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Anph\HomeBundle\Entity\SearchQueryFormType;
use Anph\HomeBundle\Entity\SearchQuery;
use Anph\HomeBundle\Entity\Sidebar\OptionSidebarItemChoice;
use Anph\HomeBundle\Builder\OptionSidebarBuilder;
use Anph\HomeBundle\Entity\Sidebar\OptionSidebarItem;
use Anph\HomeBundle\Entity\Sidebar\OptionSidebar;
use Anph\HomeBundle\Entity\SearchForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @param string $_route      Matched route name
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     *
     * @return void
     */
    public function indexAction($_route, $query_terms = null, $page = 1)
    {
        $request = $this->getRequest();
        $session = $request->getSession();

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
                    'query_terms'   => $query_terms,
                    'page'          => $page
                )
            )
        );

        $builder = new OptionSidebarBuilder($sidebar);
        $templateVars = array(
            'current_route' => $_route,
            'q'             => $query_terms,
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

            if ( $request->get('filter_field') ) {

                $filter_field = $request->get('filter_field');
                $filter_value = array($request->get('filter_value'));

                if ( isset($filters[$filter_field])
                    && is_array($filters[$filter_field])
                    && !in_array($filter_value[0], $filters[$filter_field])
                ) {
                    $filter_value = array_push(
                        $filters[$filter_field],
                        $filter_value[0]
                    );
                }
                $filters[$filter_field] = $filter_value;
                $session->set('filters', $filters);
            }

            //Add filters to container
            $container->setFilters($filters);
            if ( count($filters) > 0 ) {
                $templateVars['filters'] = $filters;
            }

            $factory = $this->get("anph.home.solarium_query_factory");
            $searchResults = $factory->performQuery($container);
            $hlSearchResults = $factory->getHighlighting();
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
            $templateVars['facets'] = $facets;

            $templateVars['resultStart'] = ($page - 1) * $resultByPage + 1;
            $resultEnd = ($page - 1) * $resultByPage + $resultByPage;
            if ( $resultEnd > $resultCount ) {
                $resultEnd = $resultCount;
            }
            $templateVars['resultEnd'] = $resultEnd;

        } else {
            $form = $this->createForm(new SearchQueryFormType(), new SearchQuery());
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
            'AnphHomeBundle:Default:index.html.twig',
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
     * @param string  $_route   Matched route name
     * @param string  $part     Part to browse
     * @param boolean $show_all Show all results
     *
     * @return void
     */
    public function browseAction($_route, $part = null, $show_all = false)
    {
        $templateVars = array(
            'current_route' => $_route,
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
            'AnphHomeBundle:Default:browse.html.twig',
            $templateVars
        );
    }
}
