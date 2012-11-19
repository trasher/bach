<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
class DynamicField {
	
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
	
	function __construct($name, $type, $indexed, $stored, $multiValued, $required, $defaut, $omitNorms,$omitTermFreqAndPositions,$omitPosition,$termVectors,$termPositions, $termOffsets){
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
}
