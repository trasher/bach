<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
class Analyzer {
	// Without inner tags
	// Attributs
	private $class;
	
	// With inner tags: tokenizer, charFilter, filter
	// Attributs
	private $type;
	// Content
	private $tokenizer;
	private $charFilterList;
	private $filterList;
	
	
	public function __construct($class){
		$this->class = $class;
	}
}
