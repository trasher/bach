<?php
/**
 * Bach users
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bach users
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
