<?php

namespace Anph\HomeBundle\Entity;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

class SearchQueryFormType extends AbstractType
{
	protected $query;
	
	private $value = "";
	
	public function __construct($value = ""){
		$this->value = $value;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		 	->add('query', 'text',
            		array(	'attr' => array('placeholder' 	=> 'Tapez votre recherche',
            								'class'			=>	'input-big span12',
            								'value'			=>	$this->value)));
	}
	
	public function getName()
	{
		return 'searchQuery';
	}	
}