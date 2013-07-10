<?php

namespace Bach\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

class Stick
{
	private $ref;
	private $area = null;
	
	public function __construct($block12, $areasData){
		$this->parse($block12, $areasData);
	}
	
	public function getRef(){
		return $this->ref;
	}
	
	public function setArea(Area $a){
		$this->area = $a;
	}
	
	public function getArea(){
		return $this->area;
	}
	
	private function parse($data, $areasData){
		$this->ref = substr($data, 0, 3);
		$areaLength = intval(substr($data, 3, 4));
		$areaStart = intval(substr($data, 7, 5));
		$this->area = new Area(substr($areasData,$areaStart, $areaLength));
	}
}