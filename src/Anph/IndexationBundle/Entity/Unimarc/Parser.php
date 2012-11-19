<?php 

namespace Anph\IndexationBundle\Entity\Unimarc;

use Symfony\Component\Finder\SplFileInfo;

class Parser
{
	public function __construct(SplFileInfo $fileInfo){
		$content = file_get_contents($fileInfo->getRelativePath());
		$labelPart = substr($content,0,24);
		$label = new Label($labelPart);
		$indice = strpos($content, '');
		$size  = strlen($content);
		//echo ("jfolflflflfllflflfl" .$size);
		$blocIdent = substr($content, 24, $indice-24);
		$blocInfo = substr($content, $indice, $size - $indice);
		$ident = new Identification($blocIdent, $blocInfo);
		
		//echo ("dkkkkkkkkkzejpozoproprepoopreore" .$blocInfo);
		//echo ("jdskdldldldldldlllllll" .strlen(string $content));
		//echo ("hfielkrlkllrlrlrlrlrl" .$blocIdent);
		
	}
	
	
	
	
	
}

?>