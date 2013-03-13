<?php

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


class DefaultController extends Controller
{
    public function indexAction()
    {
    	$form = $this->createForm(new SearchQueryFormType(), new SearchQuery());
		
    	$formActionUrlParams = $this->getRequest()->query->all();
    	
    	if(count($formActionUrlParams) > 0){
    		$formAction = $this->get("router")->generate("anph_home_homepage_search_process")."?".http_build_query($formActionUrlParams);
    	}else{
    		$formAction = $this->get("router")->generate("anph_home_homepage_search_process");
    	}
    	
    
    	
        // Construction de la barre de gauche comprenant les options de recherche
        $sidebar = new OptionSidebar();
        
        $languageItem = new OptionSidebarItem("Langue des documents","qo_lg","fr");
        $languageItem
        	->appendChoice(new OptionSidebarItemChoice("Français", "fr"))
        	->appendChoice(new OptionSidebarItemChoice("Anglais", "en"))
        ;
        $sidebar->append($languageItem);
        
        $resultsItem = new OptionSidebarItem("Nombre de résultats par page","qo_pr",10);
        $resultsItem
        	->appendChoice(new OptionSidebarItemChoice("10", 10))
        	->appendChoice(new OptionSidebarItemChoice("20", 20))
        	->appendChoice(new OptionSidebarItemChoice("50", 50))
        ;
        $sidebar->append($resultsItem);
        
        $picturesItem = new OptionSidebarItem("Afficher les images","qo_dp",1);
        $picturesItem
        	->appendChoice(new OptionSidebarItemChoice("Oui", 1))
        	->appendChoice(new OptionSidebarItemChoice("Non", 0))
        ;
        $sidebar->append($picturesItem);

        $sidebar->bind($this->getRequest());
        
        
        if(!is_null($this->getRequest()->query->get("q"))){
        	// On effectue une recherche	
        	
        	$container = new SolariumQueryContainer();
        	$container->setField("language", $sidebar->getItemValue("qo_lg"));
        	$container->setField("pageResults", $sidebar->getItemValue("qo_pr"));
        	$container->setField("displayPicture", $sidebar->getItemValue("qo_dp"));       

        	$searchResults = $this->get("anph.home.solarium_query_factory")->performQuery($container);
        	
        	$resultCount = $searchResults->getNumFound();
        }        
        
        $builder = new OptionSidebarBuilder($sidebar);
        
        return $this->render('AnphHomeBundle:Default:index.html.twig', array(
            'form' 		=> 	$form->createView(),
        	'formAction'=>	$formAction,
        	'sidebar' 	=>	$builder->compileToArray(),
        	'resultCount'	=>	$resultCount
        ));
    }
    
    public function indexProcessAction(){
    	$query = new SearchQuery();
    	$form = $this->createForm(new SearchQueryFormType(), $query);
    	$redirectUrl = $this->get("router")->generate("anph_home_homepage");
    	
    	if ($this->getRequest()->isMethod('POST')) {
    		$form->bind($this->getRequest());
    		if ($form->isValid()) {
    			$q = $query->getQuery();	
    	
    	
		    	$formActionUrlParams = $this->getRequest()->query->all();
		    	
		    	if(!array_key_exists("q", $formActionUrlParams)){
		    		$formActionUrlParams["q"] = "";
		    	}
		    	
		    	$formActionUrlParams["q"] = $q;
		    	
		    	$redirectUrl = $this->get("router")->generate("anph_home_homepage")."?".http_build_query($formActionUrlParams);
		    	
		    }
    	}
    	return new RedirectResponse($redirectUrl);
    }
}
