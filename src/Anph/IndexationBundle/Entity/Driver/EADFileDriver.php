<?php

namespace Anph\IndexationBundle\Entity\Driver;

use Anph\IndexationBundle\FileDriverInterface;
use Anph\IndexationBundle\Exception\BadInputFileFormatException;
use Symfony\Component\Finder\SplFileInfo;

class EADFileDriver implements FileDriverInterface
{
	private $dom;
	
	private $domXPath;
	
	public function process(SplFileInfo $fileInfo){
		$this->DOMInit($fileInfo->getRelativePath());	// DOM initilization
		$this->validateDocument();						// We have to validate the document to be sure it is a valid EAD file
		$fields = $this->importFieldList();				// Import the fields name to process
		$result = $this->parseFile($fields);			// Parse the EAD file and return the result
		
		return $result;
	}
	
	public function getFileFormatName(){
		return "xml";
	}
	
	private function DOMInit($path){
		$this->dom = new \DOMDocument();
		$this->dom->load($path);
		$this->domXPath = new \DOMXPath($this->dom);
	}
	
	private function validateDocument(){
		$errors = array();
		
		if($this->dom->doctype->name != "ead"){
			$errors[] = "doctype";
		}
		
		if($this->dom->firstChild->nodeName != "ead"){
			$errors[] = "firstChild";
		}		
		
		if(count($errors) > 0){
			throw new BadInputFileFormatException("The EAD file is not valid");
		}else{
			return true;
		}
	}
	
	private function importFieldList(){
		$dom = new \DOMDocument();
		$dom->load(__DIR__.'/../../Resources/config/ead-fieldlist-base.xml');
		return $dom->getElementsByTagName('field');
	}
	
	private function parseFile($fields){
		$result = array();
		
		foreach($fields as $field){
			$nodes = $this->domXPath->query("//".$field->getAttribute('name'));
			$result[$field->getAttribute('name')] = array();
			
			if($nodes->length > 0){
				foreach($nodes as $key=>$node){
					$result[$field->getAttribute('name')][] = $this->processNode($node);
				}		
			}
		}
		
		return $result;
	}
	
	private function processNode(\DOMNode $node){
		if(!method_exists($this,'processNode'.ucfirst(strtolower($node->nodeName)))){
			return $node->nodeValue;
		}else{
			$method = 'processNode'.ucfirst(strtolower($node->nodeName));
			return $this->$method($node);	
		}
	}
	
	private function processNodeControlaccess(\DOMNode $node){
		$children = array();
	
		foreach($node->childNodes as $child){
			if($child->nodeName != '#text'){
				$children[$child->nodeName] = $child->nodeValue;
			}
		}	
		return $children;
	}
	
	private function processNodeDid(\DOMNode $node){
		$children = array();
		foreach($node->childNodes as $child){
			if($child->nodeName != "#text"){
				$children[$child->nodeName] = $child->nodeValue;
			}
		}	
		return $children;
	}
}
