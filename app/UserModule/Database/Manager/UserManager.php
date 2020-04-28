<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Database\Manager;

use App\BaseModule\Database\Manager\BaseManager;
use App\UserModule\Database\Entity\User;

class UserManager extends BaseManager
{
    /**
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(string $email, string $password) : User
    {
        $user = new User;
        $user->setEmail($email);
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param User $user
     * @param string $firstName
     * @param string $lastName
     * @return User
     */
    public function updateName(User $user, string $firstName, string $lastName) : User
    {
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param User $user
     * @param string $password
     * @return User
     */
    public function updatePassword(User $user, string $password) : User
    {
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function updateLastLogin(User $user) : User
    {
        $user->setLastLogin();
        $this->em->persist($user);
        $this->em->flush();

        return  $user;
    }
}