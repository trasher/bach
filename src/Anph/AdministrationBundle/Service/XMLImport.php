<?php

namespace Anph\AdministrationBundle\Service;

use Doctrine\ORM\EntityManager;

class XMLImport{

	protected $manager;

	public function __construct(EntityManager $manager){
		$this->manager = $manager;
	}

	public function importXML($sxf){
		$this->manager->persist($this->$sxf);
		$this->manager->flush();
	}
}


?>