<?php

namespace Anph\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

class Notice
{
	private $label;
	private $sticks;
	
	public function __construct($block){
		$labelLength = 24; //longueur du label
		$stickLength = 12; //longueur d'un premier bloc d'étiquette
		
		$this->label = new Label(substr($block,0,$labelLength)); //création du label
				
		$stickEnd = strpos($block, chr(30));
		$size  = strlen($block);
		
		$sticksData = substr($block, $labelLength, $stickEnd-$labelLength);
		$areasData = substr($block, $stickEnd, $size - $stickEnd);
		
		$sticksData = str_split($sticksData,$stickLength); //split tous les 12 fois.
		$sticks = array();
		
		foreach($sticksData as $stickData){
			$sticks[] = new Stick($stickData, $areasData);
		}
		
		$this->sticks = $sticks;
	}
	
	public function getLabel(){
		return $this->label;
	}
	
	public function getSticks(){
		return $this->sticks;
	}
}