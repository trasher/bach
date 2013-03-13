<?php

namespace Anph\HomeBundle\Controller;

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
    	$search = new SearchForm();

        $form = $this->createFormBuilder($search)
            ->add('query', 'text',
            		array(	'attr' => array('placeholder' 	=> 'Tapez votre recherche',
            								'class'			=>	'input-big span12')))
            ->getForm();
		
        // Construction de la barre de gauche comprenant les options de recherche
        $sidebar = new OptionSidebar();
        
        $languageItem = new OptionSidebarItem("Langue des documents","language","fr");
        $languageItem
        	->appendChoice(new OptionSidebarItemChoice("Français", "fr"))
        	->appendChoice(new OptionSidebarItemChoice("Anglais", "en"))
        ;
        $sidebar->append($languageItem);
        
        $resultsItem = new OptionSidebarItem("Nombre de résultats par page","byPage",10);
        $resultsItem
        	->appendChoice(new OptionSidebarItemChoice("10", 10))
        	->appendChoice(new OptionSidebarItemChoice("20", 20))
        	->appendChoice(new OptionSidebarItemChoice("50", 50))
        ;
        $sidebar->append($resultsItem);
        
        $picturesItem = new OptionSidebarItem("Afficher les images","showPictures",1);
        $picturesItem
        	->appendChoice(new OptionSidebarItemChoice("Oui", 1))
        	->appendChoice(new OptionSidebarItemChoice("Non", 0))
        ;
        $sidebar->append($picturesItem);
        
        $sidebar->bind($this->getRequest());
        
        $builder = new OptionSidebarBuilder($sidebar);
        
        return $this->render('AnphHomeBundle:Default:index.html.twig', array(
            'form' 		=> $form->createView(),
        	'sidebar' 	=> $builder->compileToArray()
        ));
    }
}
