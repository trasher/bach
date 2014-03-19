<?php
/**
 * Bach openid user manager
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

use Fp\OpenIdBundle\Model\UserManager;
use Fp\OpenIdBundle\Model\IdentityManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Bach openid user manager
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 */
class OpenIdUserManager extends UserManager
{
    /**
     * Constructor
     * We will use an EntityManager, so inject it via constructor
     *
     * @param IdentityManagerInterface $identityManager Identity manager
     * @param EntityManager            $entityManager   Entity manager
     */
    public function __construct(IdentityManagerInterface $identityManager,
        EntityManager $entityManager
    ) {
        parent::__construct($identityManager);

        $this->entityManager = $entityManager;
    }

    /**
     * Creates user from identity
     *
     * @param string $identity   an OpenID token. With Google it looks like:
     *  https://www.google.com/accounts/o8/id?id=SOME_RANDOM_USER_ID
     * @param array  $attributes requested attributes (explained later).
     *                           At the moment just assume there's a
     *                           'contact/email' key
     *
     * @return UserInterface
     */
    public function createUserFromIdentity($identity, array $attributes = array())
    {
        if (false === isset($attributes['contact/email'])) {
            throw new \Exception('We need your e-mail address!');
        }

        // fetch User entities by e-mail
        $user = $this->entityManager->getRepository(
            'BachHomeBundle:User'
        )->findOneBy(
            array(
                'email' => $attributes['contact/email']
            )
        );

        if (null === $user) {
            //TODO: register user and authenticate him
            //no user found. Auth fails for now.
            throw new BadCredentialsException(
                _('No corresponding user was found!')
            );
        }

        // we create an OpenIdIdentity for this User
        $openIdIdentity = new OpenIdIdentity();
        $openIdIdentity->setIdentity($identity);
        $openIdIdentity->setAttributes($attributes);
        $openIdIdentity->setUser($user);

        $this->entityManager->persist($openIdIdentity);
        $this->entityManager->flush();

        // you must return an UserInterface instance (or throw an exception)
        return $user;
    }
}
