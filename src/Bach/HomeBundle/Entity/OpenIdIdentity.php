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

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Fp\OpenIdBundle\Entity\UserIdentity as BaseUserIdentity;
use Fp\OpenIdBundle\Model\UserIdentityInterface;

/**
 * Bach users
 *
 * @ORM\Entity
 * @ORM\Table(name="openid_identities")
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class OpenIdIdentity extends BaseUserIdentity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
      * The relation is made eager by purpose.
      * More info here: {@link https://github.com/formapro/FpOpenIdBundle/issues/54}
      *
      * @var Symfony\Component\Security\Core\User\UserInterface
      *
      * @ORM\ManyToOne(targetEntity="Bach\HomeBundle\Entity\User", fetch="EAGER")
      * @ORM\JoinColumns({
      *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
      * })
      */
    protected $user;

    /*
     * It inherits an "identity" string field,
     * and an "attributes" text field
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // your own logic (nothing for this example)
    }
}
