<?php
namespace Bach\IndexationBundle\Entity;

use Bach\IndexationBundle\ObjectTreeComponentInterface;

class ObjectTree implements ObjectTreeComponentInterface
{
    private $sheets = array();
    private $children = array();
    private $name;
	
    /**
    * The constructor
    * @param string $name The name of the tree
    */
	public function __construct($name)
	{
        $this->name = $name;
    }
	
    /**
    * Add a component to the tree
    * @param ObjectTreeComponentInterface $sheet The component to add
    */
    public function append(ObjectTreeComponentInterface $sheet)
    {
        if ($sheet instanceof ObjectTree) {
            if (array_key_exists($sheet->getName(),$this->children)
                || array_key_exists($sheet->getName(),$this->sheets)) {
                throw new \RuntimeException("ObjectTree sheet conflict name");
            } else {
                $this->children[$sheet->getName()] = $sheet;
            }
        } elseif ($sheet instanceof ObjectSheet) {
            if (array_key_exists($sheet->getName(),$this->children)
            || array_key_exists($sheet->getName(),$this->sheets)) {
                throw new \RuntimeException("ObjectTree sheet conflict name");
            } else {
                $this->sheets[$sheet->getName()] = $sheet;
            }
        }
    }
    
    /**
    * Retrieve a component from the tree
    * @param string $name The name of the component
    * @return ObjectTree|ObjectSheet The tree component
    */
    public function get($name)
    {
        foreach ($this->children as $childName=>$child) {
            if ($name == $childName) {
                return $child;
            }
        }
		
        foreach ($this->sheets as $sheetName=>$sheet) {
            if ($name == $sheetName) {
                return $sheet;
            }
        }
    }
    
    /**
    * Name Getter
    * @return string The name of the sheet
    */
    public function getName()
    {
    	return $this->name;
    }
}