<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
class Similarity {
	private $class;
	// With inner tags: str
	private $strList;
	
	public function __construct($class){
		$this->class = $class;
	}
}
