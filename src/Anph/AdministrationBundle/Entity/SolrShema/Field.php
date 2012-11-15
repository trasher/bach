<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;

class Field {
	private $indexed;
	private $stored;
	private $compressed;
	private $compressThreshold;
	private $multiValued;
	private $omitNorms;
	private $termVectors;
	private $omitTermFreqAndPositions;
	private $omitPosition;
	
	function __construct($indexed,$stored,$compressed,$compressThreshold, $multivalued, $omitNorms, $termVectors,$omitTermFreqAndPositions,$omitPosition ){
		this->$indexed = $indexed;
		this->$stored = $stored;
		this->$compressed= $compressed;
		this->$compressThreshold= $compressThreshold;
		this->$multiValued=  $multiValued;
		this->$omitNorms= $omitNorms;
		this->$termVectors= $termVectors;
		this->$omitTermFreqAndPositions = $omitTermFreqAndPositions;
		this->$omitPosition= $omitPosition;	
	}
	
	private function isIndexed(){
		return this->$indexed;
	}
	
	private function isStored(){
		return this->$stored;
	}
	
	private function isCompressed(){
		return this->$compressed;
	}
	
	private function isCompressThreshold(){
		return this->$compressThreshold;
	}
	
	private function isMultiValued(){
		return this->$multiValued;
	}
	
	private function isStored(){
		return this->$stored;
	}
	
	private function isStored(){
		return this->$stored;
	}
	
	
	
	
	


}
