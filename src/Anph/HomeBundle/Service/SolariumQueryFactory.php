<?php

namespace Anph\HomeBundle\Service;

use Symfony\Component\Finder\Finder;

use Anph\HomeBundle\Entity\SolariumQueryContainer;

use Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract;

use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;
use Doctrine\ORM\EntityManager;

class SolariumQueryFactory
{
	private $client;
	private $decorators = array();
	
	public function __construct(\Solarium_Client $client)
	{
		$this->client = $client;
		$this->searchQueryDecorators();
	}
	
	/**
	 * Perform a query into Solr
	 * @param SolariumQueryContainer $container
	 * @return \Solarium_Result_Select select
	 */
	public function performQuery(SolariumQueryContainer $container){
		$query = $this->client->createSelect();
		
		foreach($container->getFields() as $name=>$value){
			if(array_key_exists($name,$this->decorators)){
				$this->decorators[$name]->decorate($query,$value); // Décorate the query;
			}
		}
		
		return $this->client->select($query);
	}
	
	private function searchQueryDecorators() 
	{
		$finder = new Finder();
		$finder->files()->in(__DIR__.'/../Entity/SolariumQueryDecorator')->depth('== 0')->name('*.php');
		 
		foreach ($finder as $file) {
			try {
				$reflection = new \ReflectionClass('Anph\HomeBundle\Entity\SolariumQueryDecorator\\'.
						$file->getBasename(".php"));
		
				if ('Anph\HomeBundle\Entity\SolariumQueryDecoratorAbstract' == $reflection->getParentClass()->getName()) {		
					$this->registerQueryDecorator($reflection->newInstance());
				}
			} catch(\RuntimeException $e) {
			}
		}
	}
	
	private function registerQueryDecorator(SolariumQueryDecoratorAbstract $decorator){
		$this->decorators[$decorator->getTargetField()] = $decorator;
	}
}