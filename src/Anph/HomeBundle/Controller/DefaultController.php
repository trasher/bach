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
        $formAction = $this->get("router")->generate("anph_home_homepage_search_process");

        $formActionUrlParams = $this->getRequest()->query->all();
        if ( count($formActionUrlParams) > 0 ) {
            $formAction .= '?' . http_build_query($formActionUrlParams);
        }

        // Construction de la barre de gauche comprenant les options de recherche
        $sidebar = new OptionSidebar();

        $languageItem = new OptionSidebarItem("Langue des documents", "qo_lg", "fr");
        $languageItem
            ->appendChoice(new OptionSidebarItemChoice("Français", "fr"))
            ->appendChoice(new OptionSidebarItemChoice("Anglais", "en"));
        $sidebar->append($languageItem);

        $resultsItem = new OptionSidebarItem("Nombre de résultats par page", "qo_pr", 10);
        $resultsItem
            ->appendChoice(new OptionSidebarItemChoice("10", 10))
            ->appendChoice(new OptionSidebarItemChoice("20", 20))
            ->appendChoice(new OptionSidebarItemChoice("50", 50));
        $sidebar->append($resultsItem);

        $picturesItem = new OptionSidebarItem("Afficher les images", "qo_dp", 1);
        $picturesItem
            ->appendChoice(new OptionSidebarItemChoice("Oui", 1))
            ->appendChoice(new OptionSidebarItemChoice("Non", 0));
        $sidebar->append($picturesItem);

        $sidebar->bind($this->getRequest());

        if ( !is_null($this->getRequest()->query->get("q")) ) {
            // On effectue une recherche
            $form = $this->createForm(
                new SearchQueryFormType($this->getRequest()->query->get("q")),
                new SearchQuery()
            );

            $container = new SolariumQueryContainer();
            $container->setField("language", $sidebar->getItemValue("qo_lg"));
            $container->setField("displayPicture", $sidebar->getItemValue("qo_dp"));
            $container->setField("fulltext", $this->getRequest()->query->get("q"));

            if ( !is_null($this->getRequest()->query->get("p")) ) {
                $page = intval($this->getRequest()->query->get("p"));

                if ( $page < 1 ) {
                    $page = 1;
                }
            } else {
                $page = 1;
            }

            $resultByPage = 20;

            $container->setField(
                "pager",
                array(
                    "start"     => 0,
                    "offset"    => intval($sidebar->getItemValue("qo_pr"))
                )
            );

            $time = microtime(true);
            $searchResults = $this->get("anph.home.solarium_query_factory")->performQuery($container);
            $time = number_format(microtime(true)-$time, 4);
            $resultCount = $searchResults->getNumFound();

            $query = $this->get("solarium.client")->createSuggester();
            $query->setQuery($this->getRequest()->query->get("q")); //multiple terms
            $query->setDictionary('suggest');
            $query->setOnlyMorePopular(true);
            $query->setCount(10);
            //$query->setCollate(true);
            $suggestions = $this->get("solarium.client")->suggester($query);
        } else {
            $resultCount = 0;
            $time = 0;
            $form = $this->createForm(new SearchQueryFormType(), new SearchQuery()); 
            $searchResults = array();
        }

        $builder = new OptionSidebarBuilder($sidebar);

        $templateVars = array(
                'form'             =>     $form->createView(),
                'formAction'    =>    $formAction,
                'sidebar'         =>    $builder->compileToArray(),
                'resultCount'    =>    $resultCount,
                'searchResults'    =>    $searchResults,
                'time'            =>    $time
        );

        if ( isset($suggestions) ) {
            $templateVars['suggestions'] =    $suggestions;

            $queryUrlParams = $this->getRequest()->query->all();
            if ( array_key_exists("p", $queryUrlParams) ) {
                unset($queryUrlParams["q"]);
            }

            $templateVars['urlQueryPrefix'] = $this->get("router")->generate("anph_home_homepage");
            if ( count($queryUrlParams) > 0 ) {
                $templateVars['urlQueryPrefix'] .= '?' . http_build_query($queryUrlParams);
            }
            $templateVars['urlQueryPrefix'] .= '?q=$query';
        }

        return $this->render('AnphHomeBundle:Default:index.html.twig', $templateVars);
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

                $redirectUrl = $this->get("router")->generate("anph_home_homepage")."?".http_build_query($formActionUrlParams);

            }
        }
        return new RedirectResponse($redirectUrl);
    }
}
