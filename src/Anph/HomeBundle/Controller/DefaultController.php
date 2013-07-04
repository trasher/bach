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
     * @return void
     */
    public function indexAction()
    {
        $formAction = $this->get("router")
            ->generate("anph_home_homepage_search_process");

        $formActionUrlParams = $this->getRequest()->query->all();
        if ( count($formActionUrlParams) > 0 ) {
            $formAction .= '?' . http_build_query($formActionUrlParams);
        }

        // Construction de la barre de gauche comprenant les options de recherche
        $sidebar = new OptionSidebar();

        /*$languageItem = new OptionSidebarItem(
            "Langue des documents",
            "qo_lg",
            "fr"
        );
        $languageItem
            ->appendChoice(new OptionSidebarItemChoice("Français", "fr"))
            ->appendChoice(new OptionSidebarItemChoice("Anglais", "en"));
        $sidebar->append($languageItem);*/

        $resultsItem = new OptionSidebarItem(
            "Nombre de résultats par page",
            "qo_pr",
            10
        );
        $resultsItem
            ->appendChoice(new OptionSidebarItemChoice("10", 10))
            ->appendChoice(new OptionSidebarItemChoice("20", 20))
            ->appendChoice(new OptionSidebarItemChoice("50", 50));
        $sidebar->append($resultsItem);

        $picturesItem = new OptionSidebarItem(
            "Afficher les images",
            "qo_dp",
            1
        );
        $picturesItem
            ->appendChoice(new OptionSidebarItemChoice("Oui", 1))
            ->appendChoice(new OptionSidebarItemChoice("Non", 0));
        $sidebar->append($picturesItem);

        $sidebar->bind($this->getRequest());

        $builder = new OptionSidebarBuilder($sidebar);
        $templateVars = array(
            'formAction'        => $formAction,
            'sidebar'           => $builder->compileToArray(),
            'display_pics'      => $sidebar->getItemValue("qo_dp")
        );

        if ( !is_null($this->getRequest()->query->get("q")) ) {
            // On effectue une recherche
            $form = $this->createForm(
                new SearchQueryFormType($this->getRequest()->query->get("q")),
                new SearchQuery()
            );

            $container = new SolariumQueryContainer();
            $container->setField("language", $sidebar->getItemValue("qo_lg"));
            $container->setField("displayPicture", $sidebar->getItemValue("qo_dp"));
            $container->setField("main", $this->getRequest()->query->get("q"));

            $page = 1;
            if ( !is_null($this->getRequest()->query->get("p")) ) {
                $page = intval($this->getRequest()->query->get("p"));

                if ( $page < 1 ) {
                    $page = 1;
                }
            }

            $resultByPage = intval($sidebar->getItemValue("qo_pr"));

            $container->setField(
                "pager",
                array(
                    "start"     => ($page - 1) * $resultByPage,
                    "offset"    => $resultByPage
                )
            );

            $factory = $this->get("anph.home.solarium_query_factory");
            $searchResults = $factory->performQuery($container);
            $hlSearchResults = $factory->getHighlighting();
            $resultCount = $searchResults->getNumFound();

            $query = $this->get("solarium.client")->createSuggester();
            $query->setQuery(strtolower($this->getRequest()->query->get("q")));
            $query->setDictionary('suggest');
            $query->setOnlyMorePopular(true);
            $query->setCount(10);
            //$query->setCollate(true);
            $suggestions = $this->get("solarium.client")->suggester($query);

            $templateVars['resultCount'] = $resultCount;
            $templateVars['q'] = $this->getRequest()->query->get("q");
            $templateVars['page'] = $page;
            $templateVars['resultByPage'] = $resultByPage;
            $templateVars['totalPages'] = ceil($resultCount/$resultByPage);
            $templateVars['searchResults'] = $searchResults;
            $templateVars['hlSearchResults'] = $hlSearchResults;

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

            $queryUrlParams = $this->getRequest()->query->all();
            if ( array_key_exists('q', $queryUrlParams) ) {
                unset($queryUrlParams['q']);
            }

            $templateVars['urlQueryPrefix'] = $this->get("router")
                ->generate("anph_home_homepage");
            if ( count($queryUrlParams) > 0 ) {
                $templateVars['urlQueryPrefix'] .= '?' .
                    http_build_query($queryUrlParams) . '&';
            } else {
                $templateVars['urlQueryPrefix'] .= '?';
            }
            $templateVars['urlQueryPrefix'] .= 'q=$query';
        }

        //pagination prefix... not really cool, but that works for now.
        $queryUrlParams = $this->getRequest()->query->all();
        if ( array_key_exists('p', $queryUrlParams) ) {
            //remove p, if existing
            unset($queryUrlParams['p']);
        }
        $templateVars['paginationPath'] = $this->get("router")
            ->generate("anph_home_homepage");
        if ( count($queryUrlParams) > 0 ) {
            $templateVars['paginationPath'] .= '?' .
                http_build_query($queryUrlParams) . '&';
        } else {
            $templateVars['paginationPath'] .= '?';
        }
        $templateVars['paginationPath'] .= 'p=';

        return $this->render(
            'AnphHomeBundle:Default:index.html.twig',
            $templateVars
        );
    }

    /**
     * Main results page (unsure)?
     *
     * @return void
     */
    public function indexProcessAction()
    {
        $query = new SearchQuery();
        $form = $this->createForm(new SearchQueryFormType(), $query);
        $redirectUrl = $this->get("router")->generate("anph_home_homepage");

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $q = $query->getQuery();

                $formActionUrlParams = $this->getRequest()->query->all();

                if ( !array_key_exists("q", $formActionUrlParams) ) {
                    $formActionUrlParams["q"] = "";
                }

                $formActionUrlParams["q"] = $q;

                $redirectUrl = $this->get("router")->generate("anph_home_homepage") .
                    '?' . http_build_query($formActionUrlParams);

            }
        }
        return new RedirectResponse($redirectUrl);
    }
}
