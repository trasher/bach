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
	
	/**
	 * @return the $name
	 */
	private function getName() {
		return $this->name;
	}

	/**
	 * @return the $type
	 */
	private function getType() {
		return $this->type;
	}

	/**
	 * @return the $indexed
	 */
	private function getIndexed() {
		return $this->indexed;
	}

	/**
	 * @return the $stored
	 */
	private function getStored() {
		return $this->stored;
	}

	/**
	 * @return the $multiValued
	 */
	private function getMultiValued() {
		return $this->multiValued;
	}

	/**
	 * @return the $required
	 */
	private function getRequired() {
		return $this->required;
	}

	/**
	 * @return the $defaut
	 */
	private function getDefaut() {
		return $this->defaut;
	}

	/**
	 * @return the $omitNorms
	 */
	private function getOmitNorms() {
		return $this->omitNorms;
	}

	/**
	 * @return the $omitTermFreqAndPositions
	 */
	private function getOmitTermFreqAndPositions() {
		return $this->omitTermFreqAndPositions;
	}

	/**
	 * @return the $omitPosition
	 */
	private function getOmitPosition() {
		return $this->omitPosition;
	}

	/**
	 * @return the $termVectors
	 */
	private function getTermVectors() {
		return $this->termVectors;
	}

	/**
	 * @return the $termPositions
	 */
	private function getTermPositions() {
		return $this->termPositions;
	}

	/**
	 * @return the $termOffsets
	 */
	private function getTermOffsets() {
		return $this->termOffsets;
	}

	/**
	 * @param field_type $name
	 */
	private function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $type
	 */
	private function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param field_type $indexed
	 */
	private function setIndexed($indexed) {
		$this->indexed = $indexed;
	}

	/**
	 * @param field_type $stored
	 */
	private function setStored($stored) {
		$this->stored = $stored;
	}

	/**
	 * @param field_type $multiValued
	 */
	private function setMultiValued($multiValued) {
		$this->multiValued = $multiValued;
	}

	/**
	 * @param field_type $required
	 */
	private function setRequired($required) {
		$this->required = $required;
	}

	/**
	 * @param field_type $defaut
	 */
	private function setDefaut($defaut) {
		$this->defaut = $defaut;
	}

	/**
	 * @param field_type $omitNorms
	 */
	private function setOmitNorms($omitNorms) {
		$this->omitNorms = $omitNorms;
	}

	/**
	 * @param field_type $omitTermFreqAndPositions
	 */
	private function setOmitTermFreqAndPositions($omitTermFreqAndPositions) {
		$this->omitTermFreqAndPositions = $omitTermFreqAndPositions;
	}

	/**
	 * @param field_type $omitPosition
	 */
	private function setOmitPosition($omitPosition) {
		$this->omitPosition = $omitPosition;
	}

	/**
	 * @param field_type $termVectors
	 */
	private function setTermVectors($termVectors) {
		$this->termVectors = $termVectors;
	}

	/**
	 * @param field_type $termPositions
	 */
	private function setTermPositions($termPositions) {
		$this->termPositions = $termPositions;
	}

	/**
	 * @param field_type $termOffsets
	 */
	private function setTermOffsets($termOffsets) {
		$this->termOffsets = $termOffsets;
	}
	
	


}
