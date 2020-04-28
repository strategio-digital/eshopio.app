<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\AllowedDay;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class AllowedDayRepository extends EntityRepository
{
    /**
     * All Types of allowed days
     */
    const ALLOWED_DAYS = [
        AllowedDay::MONDAY,
        AllowedDay::TUESDAY,
        AllowedDay::WEDNESDAY,
        AllowedDay::THURSDAY,
        AllowedDay::FRIDAY,
        AllowedDay::SATURDAY,
        AllowedDay::SUNDAY
    ];

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
        $this->repository = $em->getRepository(AllowedDay::class);
    }

    /**
     * @return ArrayCollection|AllowedDay[]
     */
    public function findAll() : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('AllowedDay');

        /** @var AllowedDay[] $allowedDays */
        $allowedDays = $qb
            ->addSelect('AllowedDay')
            ->orderBy('AllowedDay.id')
            ->getQuery()
            ->getResult();

        return new ArrayCollection($allowedDays);
    }

    /**
     * @param int[] $ids
     * @return ArrayCollection
     */
    public function findByIdArray(array $ids = []) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('AllowedDay');

        /** @var AllowedDay[] $allowedDays */
        $allowedDays = $qb
            ->addSelect('AllowedDay')
            ->where('AllowedDay.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($allowedDays);
    }

}
