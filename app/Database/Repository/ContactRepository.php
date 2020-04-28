<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\Contact;
use App\Database\Entity\EmailCampaign;
use App\Database\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class ContactRepository extends EntityRepository
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
     * ContactRepository constructor.
     * @param EntityManagerDecorator $em
     */
    public function __construct(EntityManagerDecorator $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Contact::class);
    }

    /**
     * @param Project $project
     * @param string $email
     * @return Contact|null
     */
    public function findOneByProjectAndEmail(Project $project, string $email) : ?Contact
    {
        /** @var Contact $contact */
        $contact = $this->repository->findOneBy([
            'project' => $project,
            'email' => $email
        ]);

        return $contact;
    }

    /**
     * @param Project $project
     * @param string $phone
     * @return Contact|null
     */
    public function findOneByProjectAndPhone(Project $project, string $phone) : ?Contact
    {
        /** @var Contact $contact */
        $contact = $this->repository->findOneBy([
            'project' => $project,
            'phone' => $phone
        ]);

        return $contact;
    }

    /**
     * @param Project $project
     * @return ArrayCollection|Contact[]
     */
    public function findAllByProject(Project $project) : Collection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Contact');

        $contacts = $qb
            ->addSelect('Contact')
            ->addSelect('Project')
            ->addSelect('Segment')
            ->innerJoin('Contact.project', 'Project')
            ->leftJoin('Contact.segment', 'Segment')
            ->where('Project = :project')
            ->setParameter('project', $project)
            ->addOrderBy('Contact.segment')
            ->addOrderBy('Contact.name')
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contacts);
    }

    /**
     * @param int $id
     * @return Contact|null
     */
    public function findOneById(int $id) : ?Contact
    {
        /** @var Contact $contact */
        $contact = $this->repository->find($id);
        return $contact;
    }

    public function findPaginatedByProject(Project $project, int $limit, int $offset) : Collection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Contact');

        $contacts = $qb
            ->addSelect('Contact')
            ->addSelect('Project')
            ->addSelect('Segment')
            ->innerJoin('Contact.project', 'Project')
            ->leftJoin('Contact.segment', 'Segment')
            ->where('Project = :project')
            ->setParameter('project', $project)
            ->addOrderBy('Contact.segment')
            ->addOrderBy('Contact.name')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contacts);
    }

    /**
     * @param Project $project
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByProject(Project $project) : int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Contact');

        $count = $qb
            ->select('count(Contact)')
            ->innerJoin('Contact.project', 'Project')
            ->where('Project = :project')
            ->setParameter('project', $project)
            ->getQuery();

        return $count->getSingleScalarResult();
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return ArrayCollection|Contact[]
     */
    public function findByEmailCampaignEmailNotNull(EmailCampaign $emailCampaign) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Contact');

        $contacts = $qb
            ->addSelect('Contact')
            ->addSelect('Segment')
            ->innerJoin('Contact.segment', 'Segment')
            ->innerJoin('Segment.emailCampaigns', 'EmailCampaign')
            ->andWhere('Contact.email IS NOT NULL')
            ->andWhere('EmailCampaign = :emailCampaign')
            ->setParameter('emailCampaign', $emailCampaign)
            ->getQuery()
            ->getResult();

        return new ArrayCollection($contacts);
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return int
     * @throws NonUniqueResultException
     */
    public function countByEmailCampaignEmailNotNull(EmailCampaign $emailCampaign) : int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('Contact');

        /** @var Query $query */
        $query = $qb
            ->select('count(Contact)')
            ->innerJoin('Contact.segment', 'Segment')
            ->innerJoin('Segment.emailCampaigns', 'EmailCampaign')
            ->andWhere('Contact.email IS NOT NULL')
            ->andWhere('EmailCampaign = :emailCampaign')
            ->setParameter('emailCampaign', $emailCampaign)
            ->groupBy('EmailCampaign')
            ->getQuery();

        try {
            $count = $query->getSingleScalarResult();
        } catch (NoResultException $exception) {
            return 0;
        }

        return $count;
    }
}
