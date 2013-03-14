<?php

namespace Anph\HomeBundle\Entity;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

class SearchQueryFormType extends AbstractType
{
	protected $query;
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		 	->add('query', 'text',
            		array(	'attr' => array('placeholder' 	=> 'Tapez votre recherche',
            								'class'			=>	'input-big span12')));
	}
	
	public function getName()
	{
		return 'searchQuery';
	}	
}