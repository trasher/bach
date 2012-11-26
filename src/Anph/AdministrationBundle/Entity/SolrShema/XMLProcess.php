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

	public function _construct($file){

		$this->dom = new DomDocument();
		$this->dom->load($file);
		$this->xmlRoot=$dom->documentElement;
		$this->sxf = new SolrXMLFile();
		$this->sxf->setName($file);
		$this->sxf->setPath($file);
		$this->sxf->save();
		$em = $this->getDoctrine()->getManager();
		$em->persist($sxf);
		$em->flush();
	}

	public function importXML(){
		importXML($xmlRoot);
		echo 'test';
	}

	public function importXML($node){

		if($node->nodeType == XML_TEXT_NODE){
			$textNode = new SolrXMLElement();
			$textNode->setValue($node->nodeValue);
			$textNode->setBalise($node->tagName);
			$textNode->setFile($sxf->getSolrXMLFileID());
			if($root!=Null){
		  $textNode->setRoot($root);
			}else $textNode->setRoot("none");
			$this->sxf->save();
			if($node->hasAttributes()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($textNode);
				$em->flush();
				 
				$attributes = $node->attributes;
				 
				if(!is_null($attributes)) {
					foreach ($attributes as $key => $attr) {
				
						$newAttribute= new SolrXMLAttribute();
						$newAttribute->setAttributeName($attr->name);
						$newAttribute->setAttributeValue($attr->value);
						$newAttribute->setElement($textNode->getSolrXMLElementID());
						$newAttribute->save();
					}
				}
			}
		}else{
			$newNode = new SolrXMLElement();
			$newNode->setValue($node->nodeValue);
			$newNode->setBalise($node->tagName);
			$newNode->setFile($sxf->getSolrXMLFileID());
			if($root!=Null){
				$textNode->setRoot($root);
			}else $textNode->setRoot("none");
			$newNode->save();
				
			if($node->hasAttributes()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($newNode);
				$em->flush();
					
				$attributes = $node->attributes;
					
				if(!is_null($attributes)) {
					foreach ($attributes as $key => $attr) {
						
						$newAttribute= new SolrXMLAttribute();
						$newAttribute->setAttributeName($attr->name);
						$newAttribute->setAttributeValue($attr->value);
						$newAttribute->setElement($newNode->getSolrXMLElementID());
						$newAttribute->save();
						
					}
				}
			}
				
			if($node->hasChildNodes()){
				$em = $this->getDoctrine()->getManager();
				$em->persist($newNode);
				$em->flush();
				$this->root=$newNode->getSolrXMLElementID();
				foreach($node->childNodes as $doc){
					importXML($doc);
				}
			}
		}
	}


}


?>