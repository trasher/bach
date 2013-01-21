<?php

namespace Anph\IndexationBundle\Entity\UniversalFileFormat;
use Anph\IndexationBundle\Entity\UniversalFileFormat;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="EADUniversalFileFormat")
 */
class EADFileFormat extends UniversalFileFormat {
	protected $archeDescRootUnitid, $archeDescRootUnittitle, $archeDescRootUnitdate, $archeDescRootPhysdesc, $archeDescRootRepository, $archeDescRootLangmaterial, $archeDescRootOrigination, $archeDescRootAcqinfo, $archeDescRootScopecontent, $archeDescRootAccruals, $archeDescRootArrangement, $archeDescRootAccessrestrict, $archeDescRootLegalstatus, $archeDescRootUserestrict, $archeDescRootOriginalsloc, $archeDescRootRelatedmaterial, $archeDescRootOdd, $archeDescRootProcessinfo, $archeDescRootControlaccess;

	protected $cUnitid, $cUnittitle, $cCopcontent, $cControlacces, $cDaoloc;

	public function getArcheDescRootUnitid() {
		return $this->archeDescRootUnitid;
	}

	public function setArcheDescRootUnitid($archeDescRootUnitid) {
		$this->archeDescRootUnitid = $archeDescRootUnitid;
	}

	public function getArcheDescRootUnittitle() {
		return $this->archeDescRootUnittitle;
	}

	public function setArcheDescRootUnittitle($archeDescRootUnittitle) {
		$this->archeDescRootUnittitle = $archeDescRootUnittitle;
	}

	public function getArcheDescRootUnitdate() {
		return $this->archeDescRootUnitdate;
	}

	public function setArcheDescRootUnitdate($archeDescRootUnitdate) {
		$this->archeDescRootUnitdate = $archeDescRootUnitdate;
	}

	public function getArcheDescRootPhysdesc() {
		return $this->archeDescRootPhysdesc;
	}

	public function setArcheDescRootPhysdesc($archeDescRootPhysdesc) {
		$this->archeDescRootPhysdesc = $archeDescRootPhysdesc;
	}

	public function getArcheDescRootRepository() {
		return $this->archeDescRootRepository;
	}

	public function setArcheDescRootRepository($archeDescRootRepository) {
		$this->archeDescRootRepository = $archeDescRootRepository;
	}

	public function getArcheDescRootLangmaterial() {
		return $this->archeDescRootLangmaterial;
	}

	public function setArcheDescRootLangmaterial($archeDescRootLangmaterial) {
		$this->archeDescRootLangmaterial = $archeDescRootLangmaterial;
	}

	public function getArcheDescRootOrigination() {
		return $this->archeDescRootOrigination;
	}

	public function setArcheDescRootOrigination($archeDescRootOrigination) {
		$this->archeDescRootOrigination = $archeDescRootOrigination;
	}

	public function getArcheDescRootAcqinfo() {
		return $this->archeDescRootAcqinfo;
	}

	public function setArcheDescRootAcqinfo($archeDescRootAcqinfo) {
		$this->archeDescRootAcqinfo = $archeDescRootAcqinfo;
	}

	public function getArcheDescRootScopecontent() {
		return $this->archeDescRootScopecontent;
	}

	public function setArcheDescRootScopecontent($archeDescRootScopecontent) {
		$this->archeDescRootScopecontent = $archeDescRootScopecontent;
	}

	public function getArcheDescRootAccruals() {
		return $this->archeDescRootAccruals;
	}

	public function setArcheDescRootAccruals($archeDescRootAccruals) {
		$this->archeDescRootAccruals = $archeDescRootAccruals;
	}

	public function getArcheDescRootArrangement() {
		return $this->archeDescRootArrangement;
	}

	public function setArcheDescRootArrangement($archeDescRootArrangement) {
		$this->archeDescRootArrangement = $archeDescRootArrangement;
	}

	public function getArcheDescRootAccessrestrict() {
		return $this->archeDescRootAccessrestrict;
	}

	public function setArcheDescRootAccessrestrict(
			$archeDescRootAccessrestrict) {
		$this->archeDescRootAccessrestrict = $archeDescRootAccessrestrict;
	}

	public function getArcheDescRootLegalstatus() {
		return $this->archeDescRootLegalstatus;
	}

	public function setArcheDescRootLegalstatus($archeDescRootLegalstatus) {
		$this->archeDescRootLegalstatus = $archeDescRootLegalstatus;
	}

	public function getArcheDescRootUserestrict() {
		return $this->archeDescRootUserestrict;
	}

	public function setArcheDescRootUserestrict($archeDescRootUserestrict) {
		$this->archeDescRootUserestrict = $archeDescRootUserestrict;
	}

	public function getArcheDescRootOriginalsloc() {
		return $this->archeDescRootOriginalsloc;
	}

	public function setArcheDescRootOriginalsloc($archeDescRootOriginalsloc) {
		$this->archeDescRootOriginalsloc = $archeDescRootOriginalsloc;
	}

	public function getArcheDescRootRelatedmaterial() {
		return $this->archeDescRootRelatedmaterial;
	}

	public function setArcheDescRootRelatedmaterial(
			$archeDescRootRelatedmaterial) {
		$this->archeDescRootRelatedmaterial = $archeDescRootRelatedmaterial;
	}

	public function getArcheDescRootOdd() {
		return $this->archeDescRootOdd;
	}

	public function setArcheDescRootOdd($archeDescRootOdd) {
		$this->archeDescRootOdd = $archeDescRootOdd;
	}

	public function getArcheDescRootProcessinfo() {
		return $this->archeDescRootProcessinfo;
	}

	public function setArcheDescRootProcessinfo($archeDescRootProcessinfo) {
		$this->archeDescRootProcessinfo = $archeDescRootProcessinfo;
	}

	public function getArcheDescRootControlaccess() {
		return $this->archeDescRootControlaccess;
	}

	public function setArcheDescRootControlaccess($archeDescRootControlaccess) {
		$this->archeDescRootControlaccess = $archeDescRootControlaccess;
	}

	public function getCUnitid() {
		return $this->cUnitid;
	}

	public function setCUnitid($cUnitid) {
		$this->cUnitid = $cUnitid;
	}

	public function getCUnittitle() {
		return $this->cUnittitle;
	}

	public function setCUnittitle($cUnittitle) {
		$this->cUnittitle = $cUnittitle;
	}

	public function getCCopcontent() {
		return $this->cCopcontent;
	}

	public function setCCopcontent($cCopcontent) {
		$this->cCopcontent = $cCopcontent;
	}

	public function getCControlacces() {
		return $this->cControlacces;
	}

	public function setCControlacces($cControlacces) {
		$this->cControlacces = $cControlacces;
	}

	public function getCDaoloc() {
		return $this->cDaoloc;
	}

	public function setCDaoloc($cDaoloc) {
		$this->cDaoloc = $cDaoloc;
	}

}
?>