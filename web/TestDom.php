




<?php



use Symfony\Component\Process\ProcessBuilder;

$document = new \DomDocument();

$document->load('schema.xml');

$livre = $document->documentElement;


//Affichage des fils de $livre
foreach($livre->childNodes as $node){
	if($node->nodeType ==XML_ELEMENT_NODE){
		echo 'Balise <b>', $node->tagName, '</b><br>';
		echo 'Contenu : <b>';
		echo $node->nodeValue,'</b><br>';
	}
}
?>

		