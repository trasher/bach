<?php
namespace Anph\AdministrationBundle\Service;

use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile;

use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute;

use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement;

use Doctrine\ORM\EntityManager;

class XMLImport{

	protected $manager;
	protected $dom;
	protected $root;
	protected $xmlRoot;
	protected $request;
	protected $sxf;

	public function __construct(EntityManager $manager){
		$this->manager = $manager;
	}
}

?>
