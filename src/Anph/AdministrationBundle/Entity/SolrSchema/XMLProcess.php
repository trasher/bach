<?php

namespace Anph\AdministrationBundle\Entity\SolrSchema;

use Doctrine\Tests\Common\Annotations;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLFile;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLElement;
use Anph\AdministrationBundle\Entity\SolrSchema\SolrXMLAttribute;
use Symfony\Component\HttpFoundation\Response;

class XMLProcess
{
    protected $dom;
    protected $root;
    protected $xmlRoot;
    protected $request;
    protected $sxf;
    
    public function __construct($file){
    	$this->dom = new \DomDocument();
    	$this->dom->load($file);
    	$this->xmlRoot=$this->dom->documentElement;
    	$this->sxf = new SolrXMLFile();
    	$this->sxf->setName($file);
    	$this->sxf->setPath($file);
    }
    
    public function getSXF()
    {
    	return $this->sxf;
    }
    
    public function importXML(){
    	$this->importXMLHelper($this->xmlRoot,null);
    	/*
    	$em = $this->get('doctrine')->getEntityManager();
    	$em->persist($this->$sxf);
    	$em->flush();
    	*/
    }
    
    public function importXMLHelper($node, $parent){
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
    
    			
    	}else{
    			
    		$newNode = new SolrXMLElement();
    		$newNode->setValue($node->nodeValue);
    		$newNode->setBalise($node->nodeName);
    		//$newNode->setFile($sxf->getSolrXMLFileID());
    			
    		if ($node->hasAttributes()) {
    			$attributes = $node->attributes;
    			if(!is_null($attributes)) {
    				foreach ($attributes as $key => $attr) {
    					$newAttribute= new SolrXMLAttribute();
    					$newAttribute->setAttributeName($attr->name);
    					$newAttribute->setAttributeValue($attr->value);
    					$newNode->addAttribute($newAttribute);
    				}
    			}
    		}
    			
    		if ($node->hasChildNodes()) {
    			foreach($node->childNodes as $child){
    				$this->importXMLHelper($child,$newNode);
    			}
    		}
    			
    		if ($parent!=null) {
    			$parent->addElement($newNode);
    			$this->sxf->addElement($parent);
    		} else {
    		    $this->sxf->addElement($newNode);
    		}
    	}
    }
}