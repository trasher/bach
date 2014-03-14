<?php
/**
 * Xhprof GUI
 *
 * PHP version 5
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jns\Bundle\XhprofBundle\Entity\XhprofDetail as BaseXhprofDetail;

/**
 * Xhprof GUI
 *
 * @ORM\Entity
 * @ORM\Table(name="details")
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class XhprofDetail extends BaseXhprofDetail
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", unique=true, length=17, nullable=false)
     * @ORM\Id
     */
    protected $id;

}
