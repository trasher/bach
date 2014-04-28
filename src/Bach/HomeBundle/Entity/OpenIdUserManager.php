<?php
/**
 * Bach openid user manager
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Authentication
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
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
