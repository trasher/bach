<?php 

namespace Anph\IndexationBundle\Entity\UniversalFileFormat;

use Anph\IndexationBundle\Entity\UniversalFileFormat;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
* @ORM\Table(name="EADUniversalFileFormat")
*/
class EADFileFormat extends UniversalFileFormat
{
	protected $archeDescRootUnitid,
                    $archeDescRootUnittitle,
                    $archeDescRootUnitdate,
                    $archeDescRootPhysdesc,
                    $archeDescRootRepository,
                    $archeDescRootLangmaterial,
                   	$archeDescRootOrigination,
                    $archeDescRootAcqinfo,
                    $archeDescRootScopecontent,
                    $archeDescRootAccruals,
                    $archeDescRootArrangement,
                    $archeDescRootAccessrestrict,
                    $archeDescRootLegalstatus,
                    $archeDescRootUserestrict,
                    $archeDescRootOriginalsloc,
                    $archeDescRootRelatedmaterial,
                    $archeDescRootOdd,
                    $archeDescRootProcessinfo,
                    $archeDescRootControlaccess;
	
	protected $cUnitid,
                    $cUnittitle,
                    $cCopcontent,
                    $cControlacces,
                    $cDaoloc;
	
}
?>