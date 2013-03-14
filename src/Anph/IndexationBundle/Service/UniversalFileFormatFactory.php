<?php 

namespace Anph\IndexationBundle\Service;

use Anph\IndexationBundle\Entity\UniversalFileFormat;

class UniversalFileFormatFactory
{
	public function __construct()
	{
		
	}
	
	public function build($data, $class)
	{
		if (class_exists($class)) {
			$universal = new $class($data);
		} else {
			$universal = new UniversalFileFormat($data);
		}
		
		return $universal;
	}
}
?>