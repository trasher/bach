<?php

namespace Anph\IndexationBundle\Service;

use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Anph\IndexationBundle\Exception\UnknownDriverParserException;
use Doctrine\ORM\EntityManager;

class ArchFileIntegration
{
	private $manager;
	private $factory;
	private $entityManager;
	
	public function __construct(FileDriverManager $manager, DataBagFactory $factory, EntityManager $entityManager)
	{
		$this->manager = $manager;
		$this->factory = $factory;
		$this->entityManager = $entityManager;
	}
	
	public function integrate() 
	{
		$repository = $this->entityManager
		->getRepository('AnphIndexationBundle:ArchFileIntegrationTask');
		
		$tasks = $repository->findByStatus(0);
		
		foreach ($tasks as $task) {
			$spl = new \SplFileInfo($task->getFilename());
			$format = $task->getFormat();
			
			try{
				$universalFileFormats = $this->manager->convert($this->factory->encapsulate($spl),$format);
				
				foreach ($universalFileFormats as $universalFileFormat) {
					$this->entityManager->persist($universalFileFormat);
				}
				
				$task->setStatus(1);
				//$this->entityManager->remove($task);
				$this->entityManager->persist($task);
				$this->entityManager->flush();
				//return "DONE";
			}catch(BadInputFileFormatException $e){
				$task->setStatus(2);
				$this->entityManager->persist($task);
				$this->entityManager->flush();
			}catch(UnknownDriverParserException $e){
				$task->setStatus(3);
				$this->entityManager->persist($task);
				$this->entityManager->flush();
			}	
		}
		
	}
}