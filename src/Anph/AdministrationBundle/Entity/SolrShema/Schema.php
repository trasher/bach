<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
class Schema {
	// Attributs
	private $name;
	private $version;
	// Content
	private $fields;
	private $fieldTypes;
	private $uniqueKey;
	private $copyFieldList;
	private $similarity;

	public function __construct($name, $version){
		$this->name = $name;
		$this->version = $version;
	}
}
