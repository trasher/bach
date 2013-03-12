<?php

namespace Anph\AdministrationBundle\Service;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;

use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;

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

	/*public function importXML($sxf){
		$this->manager->persist($this->$sxf);
	$this->manager->flush();
	}*/



	public function getSXF()
	{
		return $this->sxf;
	}

	public function importXML($file){

		$this->dom = new \DomDocument();
		$this->dom->load($file);
		$this->xmlRoot=$this->dom->documentElement;
		$this->sxf = new SolrXMLFile();
		$this->sxf->setName($file);
		$this->sxf->setPath($file);
		$this->importXMLHelper($this->xmlRoot,null);
		$this->manager->persist($this->sxf);
		$this->manager->flush();

	}

	public function importXMLHelper($node, $parent){
		
		if($node->nodeName != '#text' && $node->nodeName != '#comment'){
			if($node->nodeType == XML_TEXT_NODE){
				$textNode = new SolrXMLElement();
				$textNode->setValue($node->nodeValue);
				$textNode->setBalise($node->nodeName);

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
				if($parent!=null){
					$parent->addElement($textNode);
					$this->sxf->addElement($parent);
				}
				else $this->sxf->addElement($textNode);


			}else
			if ($node->nodeType ==XML_ELEMENT_NODE){
				
				$newNode = new SolrXMLElement();
				$newNode->setRoot($parent);
			    $newNode->setValue($node->nodeValue);
				$newNode->setBalise($node->tagName);
				//$newNode->setFile($sxf->getSolrXMLFileID());

				if($node->hasAttributes()) {
					$attributes = $node->attributes;
					if(!is_null($attributes)) {
						foreach ($attributes as $key => $attr) {

							$newAttribute= new SolrXMLAttribute();
							$newAttribute->setAttributeName($attr->name);
							$newAttribute->setAttributeValue($attr->value);
							$newAttribute->setElement($newNode);
							$newNode->addAttribute($newAttribute);
						}
					}
				}

				if($node->hasChildNodes()){

					foreach($node->childNodes as $child){
						$this->importXMLHelper($child,$newNode);
					}
				}

				if($parent!=null){
					$parent->addElement($newNode);
					$newNode->setFile($this->sxf);
					$this->sxf->addElement($parent);
				}
				else {
					$newNode->setFile($this->sxf);
					$this->sxf->addElement($newNode);
				}
			}
		}
	}







}


?>
