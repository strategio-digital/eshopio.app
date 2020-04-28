<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\Project;
use App\Database\Entity\Segment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class SegmentRepository extends EntityRepository
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
        $this->repository = $em->getRepository(Segment::class);
    }

    /**
     * @param Project $project
     * @return ArrayCollection|Segment[]
     */
    public function findAllByProject(Project $project) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Segment');

        /** @var Segment[] $segments */
        $segments = $qb
            ->addSelect('Segment')
            ->addSelect('count(Contact) AS count')
            ->innerJoin('Segment.project', 'Project')
            ->leftJoin('Segment.contacts', 'Contact')
            ->where('Project = :project')
            ->setParameter('project', $project)
            ->groupBy('Segment')
            ->getQuery()
            ->getResult();

        $collection = new ArrayCollection;
        foreach ($segments as $segmentArray) {
            /** @var Segment $segment */
            $segment = $segmentArray[0];
            $segment->setContactsCount($segmentArray['count']);
            $collection->add($segment);
        }

        return $collection;
    }

    /**
     * @param Project $project
     * @return ArrayCollection|Segment[]
     */
    public function findAllByProjectEmailNotNull(Project $project) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Segment');

        /** @var Segment[] $segments */
        $segments = $qb
            ->addSelect('Segment')
            ->addSelect('count(Contact) AS count')
            ->innerJoin('Segment.project', 'Project')
            ->leftJoin('Segment.contacts', 'Contact')
            ->andWhere('Project = :project')
            ->setParameter('project', $project)
            ->andWhere('Contact.email IS NOT NULL')
            ->groupBy('Segment')
            ->getQuery()
            ->getResult();

        $collection = new ArrayCollection;
        foreach ($segments as $segmentArray) {
            /** @var Segment $segment */
            $segment = $segmentArray[0];
            $segment->setContactsCount($segmentArray['count']);
            $collection->add($segment);
        }

        return $collection;
    }

    /**
     * @param int $id
     * @return Project|null
     */
    public function findOneById(int $id) : ?Segment
    {
        /** @var Segment $project */
        $project = $this->repository->find($id);

        return $project;
    }

    /**
     * @param int[] $ids
     * @return ArrayCollection|Segment[]
     */
    public function findByIdArray(array $ids) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Segment');

        /** @var Segment[] $segments */
        $segments = $qb
            ->addSelect('Segment')
            ->where('Segment.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($segments);
    }
}
