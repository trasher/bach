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
}
?>