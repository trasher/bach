<?php

namespace Anph\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

class Identification 
{
	//private $num = array{};
	private $numCodes;
	
	public function __construct($blocIdent, $blocInfo) {
		$size = strlen($blocIdent);
		$indice = 0;
		$numCodes = array();
		for ($i = 0; $i <= $size; $i++) {
			$this->numCodes[$i] = substr($blocIdent,$indice,11);
			$indice = $indice + 12;
			
		}
		echo ("test" .$numCodes[0]);
		
	}
	
	
}

?>