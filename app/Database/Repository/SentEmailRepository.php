<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\SentEmail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class SentEmailRepository extends EntityRepository
{
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
        $this->repository = $em->getRepository(SentEmail::class);
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return ArrayCollection|SentEmail[]
     */
    public function findAllByEmailCampaign(EmailCampaign $emailCampaign) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        $contacts = $qb
            ->addSelect('SentEmail')
            ->addSelect('Contact')
            ->addSelect('EmailCampaign')
            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')
            ->leftJoin('SentEmail.contact', 'Contact')

            ->where('SentEmail.emailCampaign = :emailCampaign')
            ->setParameter('emailCampaign', $emailCampaign)

            ->getQuery()
            ->getResult();

        return new ArrayCollection($contacts);
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByEmailCampaign(EmailCampaign $emailCampaign) : int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        /** @var Query $query */
        $query = $qb
            ->select('count(SentEmail)')
            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')
            ->where('SentEmail.emailCampaign = :emailCampaign')
            ->setParameter('emailCampaign', $emailCampaign)
            ->getQuery();

        try {
            $count = $query->getSingleScalarResult();
        } catch (NoResultException $exception) {
            return 0;
        }

        return $count;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByEmailCampaignSentTimeNotNull(EmailCampaign $emailCampaign) : int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        /** @var Query $query */
        $query = $qb
            ->select('count(SentEmail)')
            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')
            ->andWhere('SentEmail.emailCampaign = :emailCampaign')
            ->andWhere('SentEmail.sentTime IS NOT NULL')
            ->setParameter('emailCampaign', $emailCampaign)
            ->getQuery();

        try {
            $count = $query->getSingleScalarResult();
        } catch (NoResultException $exception) {
            return 0;
        }

        return $count;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByEmailCampaignOpenCountIsNotZero(EmailCampaign $emailCampaign) : int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        /** @var Query $query */
        $query = $qb
            ->select('count(SentEmail)')
            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')

            ->andWhere('SentEmail.emailCampaign = :emailCampaign')
            ->setParameter('emailCampaign', $emailCampaign)

            ->andWhere('SentEmail.openCount != :zero')
            ->setParameter('zero', 0)

            ->getQuery();

        try {
            $count = $query->getSingleScalarResult();
        } catch (NoResultException $exception) {
            return 0;
        }

        return $count;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByEmailCampaignClickCountIsNotZero(EmailCampaign $emailCampaign) : int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        /** @var Query $query */
        $query = $qb
            ->select('count(SentEmail)')
            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')

            ->andWhere('SentEmail.emailCampaign = :emailCampaign')
            ->setParameter('emailCampaign', $emailCampaign)

            ->andWhere('SentEmail.clickCount != :zero')
            ->setParameter('zero', 0)

            ->getQuery();

        try {
            $count = $query->getSingleScalarResult();
        } catch (NoResultException $exception) {
            return 0;
        }

        return $count;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @return SentEmail|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findLastInQueue(EmailCampaign $emailCampaign) : ?SentEmail
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        /** @var Query $query */
        $sentEmail = $qb
            ->addSelect('SentEmail')
            ->addSelect('EmailCampaign')
            ->addSelect('Contact')
            ->addSelect('SmtpSetting')

            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')
            ->innerJoin('SentEmail.contact', 'Contact')
            ->innerJoin('EmailCampaign.smtpSetting', 'SmtpSetting')

            ->andWhere('EmailCampaign = :email_campaign')
            ->setParameter('email_campaign', $emailCampaign)

            // Pouze pokud už SMTP nedosáhlo limitů
            ->andWhere('SmtpSetting.reachedDayLimit < SmtpSetting.dayLimit')
            ->andWhere('SmtpSetting.reachedMinuteLimit < SmtpSetting.minuteLimit')

            // Pouze pokud jsou ještě nějaké neodeslané maily
            ->andWhere('SentEmail.sentTime IS NULL')

            // Pouze pokud existuje e-mail
            ->andWhere('Contact.email IS NOT NULL')

            ->orderBy('SentEmail.id', 'ASC')
            ->setMaxResults(1)

            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $sentEmail;
    }

    /**
     * @param int $emailCampaignId
     * @param int $sentEmailId
     * @param string $emailCampaignSecretKey
     * @param string $sentEmailSecretKey
     * @return SentEmail|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOne(int $emailCampaignId, int $sentEmailId, string $emailCampaignSecretKey, string $sentEmailSecretKey) : ?SentEmail
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('SentEmail');

        $sentEmail = $qb
            ->addSelect('SentEmail')
            ->addSelect('EmailCampaign')

            ->innerJoin('SentEmail.emailCampaign', 'EmailCampaign')

            ->andWhere('SentEmail.id = :sent_email_id')
            ->setParameter('sent_email_id', $sentEmailId)

            ->andWhere('SentEmail.secretKey = :sent_email_secret_key')
            ->setParameter('sent_email_secret_key', $sentEmailSecretKey)

            ->andWhere('EmailCampaign.id = :email_campaign_id')
            ->setParameter('email_campaign_id', $emailCampaignId)

            ->andWhere('EmailCampaign.secretKey = :email_campaign_secret_key')
            ->setParameter('email_campaign_secret_key', $emailCampaignSecretKey)

            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $sentEmail;
    }
}
