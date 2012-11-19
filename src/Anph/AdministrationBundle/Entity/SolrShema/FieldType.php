<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
/**
 * @author philippe
 *
 */
class FieldType 
{
   private $name;
   private $class;
   private $fieldTypeDescription;
   private $sortMissingFirst;
   private $sortMissingLast;
   private $indexed;
   private $stored;
   private $multiValued;
   private $omitNorms;
   private $omitTermFreqAndPositions;
   private $omitPositions;
   private $termVectors;
   private $termPositions;
   private $termOffsets;
   private $required;
   private $default;
	
   /**
    * Construct
    * @param  $name, $class, $sort_missing_first = null, $sort_missing_last = null, $indexed = null,
    *         $stored = null, $multi_valued = null, $omit_norms = null,
    *         $omit_term_freq_and_positions = null, $omit_positions = null, $term_vectors = null,
    *         $term_positions = null, $term_offsets = null, $required = null, $default = null
    */
   public function __construct($name, $class, $sort_missing_first = null, $sort_missing_last = null, $indexed = null,
                       $stored = null, $multi_valued = null, $omit_norms = null,
                       $omit_term_freq_and_positions = null, $omit_positions = null, $term_vectors = null,
                       $term_positions = null, $term_offsets = null, $required = null, $default = null)
   {
      $this->name = $name;
      $this->class = $class;
      $this->sortMissingFirst = $sort_missing_first;
      $this->sortMissingLast = $sort_missing_last;
      $this->indexed = $indexed;
      $this->stored = $stored;
      $this->multiValued = $multi_valued;
      $this->omitNorms = $omit_norms;
      $this->omitTermFreqAndPositions = $omit_term_freq_and_positions;
      $this->omitPositions = $omit_positions;
      $this->termPositions = $term_positions;
      $this->termVectors = $term_vectors;
      $this->termOffsets = $term_offsets;
      $this->required = $required;
      $this->default = $default;
   }
}
