<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class EmailCampaignRepository extends EntityRepository
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
        $this->repository = $em->getRepository(EmailCampaign::class);
    }

    /**
     * @param string $secretKey
     * @param int $id
     * @return EmailCampaign|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     * Help? https://forum.nette.org/cs/27801-doctrine-count-joined-entity
     */
    public function findOneBySecretKeyAndId(string $secretKey, int $id) : ?EmailCampaign
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('EmailCampaign');

        /** @var EmailCampaign|null $emailCampaign */
        $emailCampaign = $qb
            ->addSelect('EmailCampaign')
            ->addSelect('AllowedDay')
            ->addSelect('Segment')

            ->leftJoin('EmailCampaign.allowedDays', 'AllowedDay')
            ->leftJoin('EmailCampaign.segments', 'Segment')

            ->andWhere('EmailCampaign.secretKey = :secretKey')
            ->andWhere('EmailCampaign.id = :id')

            ->setParameter('secretKey', $secretKey)
            ->setParameter('id', $id)

            ->getQuery()
            ->getOneOrNullResult();

        return $emailCampaign;
    }

    /**
     * @param Project $project
     * @return ArrayCollection|EmailCampaign[]
     * Help? https://forum.nette.org/cs/27801-doctrine-count-joined-entity
     */
    public function findAllByProject(Project $project) : ArrayCollection
    {
        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('EmailCampaign');

        /** @var array $emailCampaigns */
        $groupByEmailCampaign = $qb
            ->addSelect('EmailCampaign')
            ->addSelect('AllowedDay')
            ->addSelect('Segment')

            ->leftJoin('EmailCampaign.segments', 'Segment')
            ->leftJoin('EmailCampaign.allowedDays', 'AllowedDay')

            ->andWhere('EmailCampaign.project = :project')
            ->setParameter('project', $project)

            ->addOrderBy('EmailCampaign.id', 'DESC')

            ->getQuery()
            ->getResult();

        return new ArrayCollection($groupByEmailCampaign);
    }

    /**
     * @return ArrayCollection|EmailCampaign[]
     * @throws \Exception
     */
    public function findAllActive() : ArrayCollection
    {
        $dateTime = new \DateTime();
        $dayInWeek = AllowedDayRepository::ALLOWED_DAYS[$dateTime->format('N') - 1];

        /** @var QueryBuilder $qb */
        $qb = $this->repository->createQueryBuilder('EmailCampaign');

        /** @var array $emailCampaigns */
        $emailCampaigns = $qb
            ->addSelect('EmailCampaign')

            ->innerJoin('EmailCampaign.allowedDays', 'AllowedDay')
            ->innerJoin('EmailCampaign.smtpSetting', 'SmtpSetting')
            ->innerJoin('EmailCampaign.sentEmails', 'SentEmail')
            ->innerJoin('SentEmail.contact', 'Contact')

            // Pouze aktivní kampaně
            ->andWhere('EmailCampaign.status = :status_active')
            ->setParameter('status_active', EmailCampaign::STATUS_ACTIVE)

            // Pokud již nastal první den, kde lze kampaň začít odesílat
            ->andWhere('EmailCampaign.startDate <= :start_date')
            ->setParameter('start_date', $dateTime->format('Y-m-d'))

            // Pokud již nastala hodina ve kterou může začít odesílat
            ->andWhere('EmailCampaign.startTime <= :start_time')
            ->setParameter('start_time', $dateTime->format('H:i:s'))

            // Pouze pokud aktuální den není ve vyloučených
            ->andWhere('AllowedDay.name = :allowed_day')
            ->setParameter('allowed_day', $dayInWeek)

            // Pouze pokud už SMTP nedosáhlo limitů
            ->andWhere('SmtpSetting.reachedDayLimit < SmtpSetting.dayLimit')
            ->andWhere('SmtpSetting.reachedMinuteLimit < SmtpSetting.minuteLimit')

            // Pouze pokud jsou ještě nějaké neodeslané maily
            ->andWhere('SentEmail.sentTime IS NULL')

            // Pouze pokud existuje e-mail
            ->andWhere('Contact.email IS NOT NULL')

            ->getQuery()
            ->getResult()
        ;

        return new ArrayCollection($emailCampaigns);
    }

    /**
     * Clear Entities
     */
    public function clear() : void
    {
        $this->em->clear();
    }
}
