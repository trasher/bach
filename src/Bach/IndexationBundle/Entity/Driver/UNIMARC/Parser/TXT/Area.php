<?php

namespace Bach\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

class Area
{
	private $content;
	private $ref = null;
	private $subAreas = array();
	
	public function __construct($block, $ref = null){
		$this->ref = $ref;
		$this->parse(trim($block));
	}
	
	public function getContent(){
		return $this->content;
	}
	
	public function getRef(){
		return $this->ref;
	}
	
	public function getSubAreas(){
		return $this->subAreas;
	}
	
	private function parse($data){
		if (($pos = strpos($data,chr(31))) !==false) {
			$subAreas = explode(chr(31), substr($data,$pos));		
			foreach ($subAreas as $subArea) {
				$length = mb_strlen($subArea);
				if ($length > 0) {
					$this->subAreas[] = new Area(mb_substr($subArea, 1, $length-1, 'ISO-8859-1'),
												mb_substr($subArea, 0, 1, 'ISO-8859-1'));
				}
			}
	
			$this->content = trim(substr($data,0,$pos));
		} else {
			$this->content = trim($data);
		}
	}
}