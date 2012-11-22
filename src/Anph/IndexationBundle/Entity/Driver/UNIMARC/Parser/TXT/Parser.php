<?php 

namespace Anph\IndexationBundle\Entity\Driver\UNIMARC\Parser\TXT;

use Anph\IndexationBundle\Entity\ObjectTree;
use Anph\IndexationBundle\Entity\ObjectSheet;
use Anph\IndexationBundle\Entity\DataBag;
use Anph\IndexationBundle\ParserInterface;

class Parser implements ParserInterface{
	
	private $tree;
	private $configuration;
	
	/**
	* {@inheritdoc }
	*/
	public function __construct(DataBag $bag, $configuration){
		$this->configuration = $configuration;
		$this->tree = new ObjectTree("root");
		$this->parse($bag);
	}
	
	/**
	* {@inheritdoc }
	*/
	public function parse(DataBag $bag){
		$content = $bag->getData();

		$noticesData = explode(chr(29),$content);
		$notices = array();
		
		foreach($noticesData as $noticeData){
			$notices[] = new Notice($noticeData);	
		}
		
		$this->tree->append(new ObjectSheet("notices"),$notices);		
	}
	
	/**
	* {@inheritdoc }
	*/
	public function getTree(){
		return $this->tree;
	}
}
?>