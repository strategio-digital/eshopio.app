<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\User;
use App\Database\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class UserRepository extends EntityRepository
{
    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $repository;

    /**
     * UserRepository constructor.
     * @param EntityManagerDecorator $em
     */
    public function __construct(EntityManagerDecorator $em)
    {
        $this->repository = $em->getRepository(User::class);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id) : ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy([
            'id' => $id
        ]);

        return $user;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email) : ?User
    {
        /** @var User $user */
        $user = $this->repository->findOneBy([
            'email' => $email
        ]);

        return $user;
    }
}
