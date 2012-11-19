<?php 

namespace Anph\IndexationBundle\Entity\Unimarc;

class Label
{
	private $longueur_notice;
	private $statut_notice;
	private $type_document;
	private $niveau_biblio;
	private $code_niveau_hierarchique;
	private $code_non_def;
	private $longueur_indicateur;
	private $longueur_souszone;
	private $adresse_BD;
	private $niveau_encodage;
	private $forme_catalogage;
	private $def_suppl;
	private $image_repertoire;
	
	
	public function __construct($data){
		$this->longueur_notice = substr($data,0,4);
		$this->statut_notice = substr($data,5,1);
		$this->type_document = substr($data,6,1);
		$this->niveau_biblio = substr($data,7,1);
		$this->code_niveau_hierarchique = substr($data,8,1);
		$this->code_non_def = substr($data,9,1);
		$this->longueur_indicateur = substr($data,10,1);
		$this->longueur_souszone = substr($data,11,1);
		$this->adresse_BD = substr($data,12,4);
		$this->niveau_encodage = substr($data,17,1);
		$this->forme_catalogage = substr($data,18,1);
		$this->def_suppl = substr($data,19,1);
		$this->image_repertoire = substr($data, 20,3 );
		
		//echo ("j'ai pas le temps mon esprit vise ailleurs     " . $this->image_repertoire);
		 
		
	}
	
	public function getStatutNotice() {
		return $this->$statut_notice;
	}
	
	public function getLongueurNotice() {
		return $this->$longueur_notice;
	}
	
	public function gettypeDocument() {
		return $this->$type_document;
	}
	
	public function getNiveauBiblio() {
		return $this->$niveau_biblio;
	}
	
	public function getCodeNiveauHierarchique() {
		return $this->$code_niveau_hierarchique;
	}
	
	public function getCodeNonDef() {
		return $this->$code_non_def;
	}
	
	public function getLongueurIndicateur() {
		return $this->$longueur_indicateur;
	}
	
	public function getLongueurSousZone() {
		return $this->$longueur_souszone;
	}
	
	public function getAdresseBD() {
		return $this->$adresse_BD;
	}
	
	public function getNiveauEncodage() {
		return $this->$niveau_encodage;
	}
	
	public function getformeCatalogage() {
		return $this->$forme_catalogage;
	}
	
	public function getDefSuppl() {
		return $this->$def_suppl;
	}
	
	public function getImageRepertoire() {
		return $this->$image_repertoire;
	}
	
	
}

?>