<?php
namespace Anph\AdministrationBundle\Entity\SolrShema;
/**
 * @author philippe
 *
 */
class FieldTypeDescription
{
  private $className;
  private $attributsList;
  
  /**
   * Construct
   * @param  $class_name  Class name for
   */
  public function __construct($class_name)
  {
    $this->className = $class_name;
    //$this->attributsList = array((object)array("type","isRequired"));
  }
}
