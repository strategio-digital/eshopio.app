<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Security;

use App\UserModule\Database\Manager\UserManager;
use App\UserModule\Database\Repository\UserRepository;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;

class Authenticator implements IAuthenticator
{
    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @var UserManager
     */
    protected UserManager $userManager;

    /**
     * @var Passwords
     */
    protected Passwords $passwords;

    /**
     * Authenticator constructor.
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     */
    public function __construct(UserRepository $userRepository, UserManager $userManager)
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->passwords = new Passwords;
    }

    /**
     * @param array $credentials
     * @return IIdentity
     * @throws AuthenticationException
     * @throws \Exception
     */
    function authenticate(array $credentials) : IIdentity
    {
        [$email, $password] = $credentials;

        $user = $this->userRepository->findOneByEmail($email);

        if (!$user || !$this->passwords->verify($password, $user->getPassword())) {
            throw new AuthenticationException("Nesprávné přihlašovací údaje");
        }

        $this->userManager->updateLastLogin($user);

        return new Identity($user->getId(), NULL, [
            'userEntity' => $user
        ]);
    }
}
