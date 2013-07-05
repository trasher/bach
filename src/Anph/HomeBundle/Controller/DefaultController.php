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
     * @param string $query_terms Term(s) we search for
     * @param int    $page        Page
     *
     * @return void
     */
    public function indexAction($query_terms = null, $page = 1)
    {
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
            10 //TODO: store and get value from session
        );
        $resultsItem
            ->appendChoice(new OptionSidebarItemChoice("10", 10))
            ->appendChoice(new OptionSidebarItemChoice("20", 20))
            ->appendChoice(new OptionSidebarItemChoice("50", 50));
        $sidebar->append($resultsItem);

        $picturesItem = new OptionSidebarItem(
            "Afficher les images",
            "qo_dp",
            1 //TODO: store and get value from session
        );
        $picturesItem
            ->appendChoice(new OptionSidebarItemChoice("Oui", 1))
            ->appendChoice(new OptionSidebarItemChoice("Non", 0));
        $sidebar->append($picturesItem);

        $sidebar->bind(
            $this->getRequest(),
            $this->get('router')->generate(
                'bach_search',
                array(
                    'query_terms'   => $this->getRequest()->get('query_terms'),
                    'page'          => $this->getRequest()->get('page')
                )
            )
        );

        $builder = new OptionSidebarBuilder($sidebar);
        $templateVars = array(
            'sidebar'           => $builder->compileToArray(),
            'display_pics'      => $sidebar->getItemValue("qo_dp")
        );

        if ( !is_null($query_terms) ) {
            // On effectue une recherche
            $form = $this->createForm(
                new SearchQueryFormType($query_terms),
                new SearchQuery()
            );

            $container = new SolariumQueryContainer();
            $container->setField("language", $sidebar->getItemValue("qo_lg"));
            $container->setField("displayPicture", $sidebar->getItemValue("qo_dp"));
            $container->setField("main", $query_terms);

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
            $query->setQuery(strtolower($query_terms));
            $query->setDictionary('suggest');
            $query->setOnlyMorePopular(true);
            $query->setCount(10);
            //$query->setCollate(true);
            $suggestions = $this->get("solarium.client")->suggester($query);

            $templateVars['resultCount'] = $resultCount;
            $templateVars['q'] = $query_terms;
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
            }
        }
        return new RedirectResponse($redirectUrl);
    }

    /**
     * Browse contents
     *
     * @param string $part Part to browse
     *
     * @return void
     */
    public function browseAction($part = null)
    {
        $templateVars = array(
            'part'  => $part
        );

        return $this->render(
            'AnphHomeBundle:Default:browse.html.twig',
            $templateVars
        );
    }
}
