<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\Project;
use App\Database\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Nette\InvalidArgumentException;
use Nettrine\ORM\EntityManagerDecorator;

class ProjectRepository extends EntityRepository
{
    /**
     * @var EntityManagerDecorator
     */
    protected EntityManagerDecorator $em;

    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $repository;

    /**
     * ProjectRepository constructor.
     * @param EntityManagerDecorator $em
     */
    public function __construct(EntityManagerDecorator $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Project::class);
    }

    /**
     * @param User $user
     * @return ArrayCollection<Project>|Project[]
     */
    public function findAllByUserWithUsers(User $user) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Project');

        $projects = $qb
            ->addSelect('Project')
            ->addSelect('User')
            ->innerJoin('Project.users', 'User')
            ->where($qb->expr()->isMemberOf(':user', 'Project.users'))
            ->setParameter(':user', $user)
            ->addOrderBy('Project.name')
            ->getQuery()
            ->getResult();

        return new ArrayCollection($projects);
    }

    /**
     * @param int $id
     * @param string $secretKey
     * @return Project|null
     */
    public function findOneByKeyAndId(int $id, string $secretKey) : ?Project
    {
        /** @var Project $project */
        $project = $this->repository->findOneBy([
            'id' => $id,
            'secretKey' => $secretKey
        ]);

        return $project;
    }

    /**
     * Be careful with using this method (for security reasons)
     * @param int $id
     * @param bool $understandSecurity
     * @return Project|null
     */
    public function findOneById(int $id, bool $understandSecurity = FALSE) : ?Project
    {
        if (!$understandSecurity) {
            throw new InvalidArgumentException('Do you really understand how to use this method?');
        }

        /** @var Project $project */
        $project = $this->repository->findOneBy([
            'id' => $id
        ]);

        return $project;
    }

    /**
     * @param int $id
     * @param string $secretKey
     * @return Project|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByKeyAndIdWithUsers(int $id, string $secretKey) : ?Project
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Project');

        /** @var Project $project */
        $project = $qb
            ->addSelect('Project')
            ->addSelect('User')
            ->innerJoin('Project.users', 'User')
            ->andWhere('Project.id = :id')
            ->andWhere('Project.secretKey = :secretKey')
            ->setParameter(':id', $id)
            ->setParameter(':secretKey', $secretKey)
            ->getQuery()
            ->getOneOrNullResult();

        return  $project;
    }

    /**
     * @param Project $project
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isAllowedForUser(Project $project, User $user) : bool
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Project');

        $project = $qb
            ->addSelect('Project')
            ->addSelect('User')
            ->innerJoin('Project.users', 'User')
            ->andWhere('Project = :project')
            ->andWhere('User = :user')
            ->setParameter(':project', $project)
            ->setParameter(':user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $project !== NULL;
    }
}
