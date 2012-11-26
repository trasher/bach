<?php

namespace Anph\AdministrationBundle\Entity\SolrShema;

 use Doctrine\Tests\Common\Annotations\Null;

use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLFile;

use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLElement;

use Anph\AdministrationBundle\Entity\SolrShema\SolrXMLAttribute;

use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\Mapping as ORM;

class XMLProcess{

	protected $dom;
	protected $root;
	protected $xmlRoot;
	protected $request;
	protected $sxf;

	 public function __construct($file){
		$this->dom = new DomDocument();
		$this->dom->load($file);
		$this->xmlRoot=$dom->documentElement;
		$this->sxf = new SolrXMLFile();
		$this->sxf->setName($file);
		$this->sxf->setPath($file);
	}

	public function importXML(){
		importXMLHelper($xmlRoot);
		$em = $this->getDoctrine()->getManager();
		$em->persist($this->$sxf);
		$em->flush();
	}

	public function importXMLHelper($node){

		if($node->nodeType == XML_TEXT_NODE){
			$textNode = new SolrXMLElement();
			$textNode->setValue($node->nodeValue);
			$textNode->setBalise($node->tagName);
			
			if($root!=Null){
		  	$textNode->setRoot($root);
			}else $textNode->setRoot("none");
			
			
			if($node->hasAttributes()) {

				$attributes = $node->attributes;
				 
				if(!is_null($attributes)) {
					foreach ($attributes as $key => $attr) {
				
						$newAttribute= new SolrXMLAttribute();
						$newAttribute->setAttributeName($attr->name);
						$newAttribute->setAttributeValue($attr->value);
						//$newAttribute->setElement($textNode->getSolrXMLElementID());
						$textNode->addAttribute($newAttribute);
					}
				}
			}
			
			$this->sxf->addElement($textNode);
			$em = $this->getDoctrine()->getManager();
			$em->persist($textNode);
			$em->flush();
			
			
		}else{
			
			$newNode = new SolrXMLElement();
			$newNode->setValue($node->nodeValue);
			$newNode->setBalise($node->tagName);
			//$newNode->setFile($sxf->getSolrXMLFileID());
			$this->sxf->addElement($newNode);
			if($root!=Null){
				$textNode->setRoot($root);
			}else $textNode->setRoot("none");
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($newNode);
			$em->flush();
			
			if($node->hasAttributes()) {
				$attributes = $node->attributes;	
				if(!is_null($attributes)) {
					foreach ($attributes as $key => $attr) {
						
						$newAttribute= new SolrXMLAttribute();
						$newAttribute->setAttributeName($attr->name);
						$newAttribute->setAttributeValue($attr->value);
						$newAttribute->setElement($newNode->getSolrXMLElementID());
						$newNode->addAttribute($newAttribute);
	
					}
				}
			}
				
			if($node->hasChildNodes()){
				$this->root=$newNode->getSolrXMLElementID();
				foreach($node->childNodes as $doc){
					importXMLHelper($doc);
				}
			}
		}
	}


}


?>