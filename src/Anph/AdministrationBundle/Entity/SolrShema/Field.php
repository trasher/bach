<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;

class Field {
	
	private $name;
	private $type;
	private $indexed;
	private $stored;
	private $multiValued;
	private $required;
	private $defaut;
	private $omitNorms;
	private $omitTermFreqAndPositions;
	private $omitPosition;
	private $termVectors;
	private $termPositions;
	private $termOffsets;
	

	
	function __construct($name, $type, $indexed, $stored, $multiValued, $required, $defaut, $omitNorms,$omitTermFreqAndPositions,$omitPosition,$termVectors,$termPositions, $termOffsets ){
		$this->name=$name;
		$this->type=$type;
		$this->indexed = $indexed;
		$this->stored = $stored;
		$this->multiValued=  $multiValued;
		$this->required=$required;
		$this->defaut=$defaut;
		$this->omitNorms= $omitNorms;
		$this->omitTermFreqAndPositions = $omitTermFreqAndPositions;
		$this->omitPosition= $omitPosition;	
		$this->termVectors= $termVectors;
		$this->termPositions=$termPositions;
		$this->termOffsets=$termOffsets;
		
	}
	
	private function getName(){
		return $this->name;
	}

	private function getType(){
		return $this->type;
	}
	
	private function isIndexed(){
		return $this->indexed;
	}
	
	private function isStored(){
		return $this->stored;
	}

	
	private function isMultiValued(){
		return $this->multiValued;
	}
	
	private function isRequired(){
		return $this->required;
	}
	
	private function isOmitNorms(){
		return $this->omitNorms;
	}
	
	private function isOmitPosition(){
		return $this->omitPosition;
	}
	
	private function isOmitTermFreqAndPositions(){
		return $this->omitTermFreqAndPositions;
	}
	
 	private function isTermOffsets(){
		return $this->termOffsets;
	}
	
	private function isTermPositions(){
		return $this->termPositions;
	}
	
	private function isTermVectors(){
		return $this->termVectors;
	}
	
	


}
