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
	protected $accessRestrict;
	protected $abbr;
	protected $abstract;
	protected $accruals;
	protected $acqinfo;
	protected $address;
	protected $altformavail;
	protected $appraisal;
	protected $archref;
	protected $arrangement;
	protected $author;
	protected $bibliography;
	protected $bibref;
	protected $bibseries;
	protected $bioghist;
	protected $container;
	protected $controlaccess;
	protected $custodhist;
	protected $date;
	protected $daodesc;
	
	
	
	
}
?>