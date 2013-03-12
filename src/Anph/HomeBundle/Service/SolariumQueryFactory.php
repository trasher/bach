<?php

namespace Anph\IndexationBundle\Service;

use Anph\HomeBundle\Entity\SolariumQueryContainer;

use Anph\HomeBundle\Entity\SolariumQueryDecorator;

use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;
use Doctrine\ORM\EntityManager;

class SolariumQueryFactory
{
	private $client;
	private $decorators = array();
	
	public function __construct(\Solarium_Client $client)
	{
		$this->client;
		$this->searchQueryDecorators();
	}
	
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
				$reflection = new \ReflectionClass('Anph\HomeBundle\Entity\Decorator\\'.
						$file->getBasename(".php"));
		
				if ('Anph\HomeBundle\Entity\SolariumQueryDecorator' == $reflection->getParentClass()->getName()) {		
					$this->registerQueryDecorator($reflection->newInstance());
				}
			} catch(\RuntimeException $e) {
			}
		}
	}
	
	private function registerQueryDecorator(SolariumQueryDecorator $decorator){
		$this->decorators[$decorator->getTargetField()] = $decorator;
	}
}