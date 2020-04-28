<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\Project;
use App\Database\Entity\SmtpSetting;

class SmtpSettingManager extends BaseManager
{
    /**
     * @param Project $project
     * @param string $host
     * @param string $senderEmail
     * @param string $senderName
     * @param string $username
     * @param string $password
     * @param string $secure
     * @param int $port
     * @param int $minuteLimit
     * @param int $dayLimit
     * @return SmtpSetting
     */
    public function create(Project $project, string $senderEmail, string $senderName, string $host, string $username, string $password, string $secure, int $port, int $minuteLimit, int $dayLimit) : SmtpSetting
    {
        $smtpSetting = new SmtpSetting;
        $smtpSetting->setProject($project);
        $smtpSetting->setSenderEmail($senderEmail);
        $smtpSetting->setSenderName($senderName);
        $smtpSetting->setHost($host);
        $smtpSetting->setUsername($username);
        $smtpSetting->setPassword($password);
        $smtpSetting->setSecure($secure);
        $smtpSetting->setPort($port);
        $smtpSetting->setMinuteLimit($minuteLimit);
        $smtpSetting->setDayLimit($dayLimit);

        $this->em->persist($smtpSetting);
        $this->em->flush();

        return $smtpSetting;
    }

    /**
     * @param SmtpSetting $smtpSetting
     * @param string $senderEmail
     * @param string $senderName
     * @param string $host
     * @param string $username
     * @param string $secure
     * @param int $port
     * @param int $minuteLimit
     * @param int $dayLimit
     * @return SmtpSetting
     */
    public function update(SmtpSetting $smtpSetting, string $senderEmail, string $senderName,  string $host, string $username, string $secure, int $port, int $minuteLimit, int $dayLimit) : SmtpSetting
    {
        $smtpSetting->setSenderEmail($senderEmail);
        $smtpSetting->setSenderName($senderName);
        $smtpSetting->setHost($host);
        $smtpSetting->setUsername($username);
        $smtpSetting->setSecure($secure);
        $smtpSetting->setPort($port);
        $smtpSetting->setMinuteLimit($minuteLimit);
        $smtpSetting->setDayLimit($dayLimit);

        $this->em->persist($smtpSetting);
        $this->em->flush();

        return $smtpSetting;
    }

    /**
     * @param SmtpSetting $smtpSetting
     * @param string $password
     * @return SmtpSetting
     */
    public function updatePassword(SmtpSetting $smtpSetting, string $password) : SmtpSetting
    {
        $smtpSetting->setPassword($password);
        $this->em->persist($smtpSetting);
        $this->em->flush();

        return $smtpSetting;
    }

    /**
     * @param SmtpSetting $smtpSetting
     * @return SmtpSetting
     * @throws \Exception
     */
    public function increaseLimits(SmtpSetting $smtpSetting) : SmtpSetting
    {
        $smtpSetting->increaseLimits();

        $this->em->persist($smtpSetting);
        $this->em->flush();

        return $smtpSetting;
    }

    /**
     * @param SmtpSetting $smtpSetting
     * @return SmtpSetting
     * @throws \Exception
     */
    public function decreaseLimits(SmtpSetting $smtpSetting) : SmtpSetting
    {
        $smtpSetting->decreaseLimits();

        $this->em->persist($smtpSetting);
        $this->em->flush();

        return $smtpSetting;
    }

    /**
     * @param SmtpSetting $smtpSetting
     */
    public function remove(SmtpSetting $smtpSetting) : void
    {
        $this->em->remove($smtpSetting);
        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function resetDayLimits() : void
    {
        $dateTime = new \DateTime();

        $qb = $this->em->createQueryBuilder()
            ->update(SmtpSetting::class, 'SmtpSetting')
            ->set('SmtpSetting.reachedDayLimit', ':zero')

            ->andWhere('SmtpSetting.reachedDayLimit != :zero')
            ->setParameter('zero', 0)

            ->andWhere('date(SmtpSetting.lastUsage) < :actual_date')
            ->setParameter('actual_date',  $dateTime->format('Y-m-d'))

            ->getQuery()
        ;

        $qb->execute();
    }

    /**
     *
     */
    public function resetMinuteLimits() : void
    {
        $dateTime = new \DateTime();
        $qb = $this->em->createQueryBuilder()
            ->update(SmtpSetting::class, 'SmtpSetting')
            ->set('SmtpSetting.reachedMinuteLimit', ':zero')

            ->andWhere('SmtpSetting.reachedMinuteLimit != :zero')
            ->setParameter('zero', 0)

            ->andWhere('SmtpSetting.lastUsage < :actual_datetime')
            ->setParameter('actual_datetime',  $dateTime->format('Y-m-d H:i:00'))

            ->getQuery()
        ;

        $qb->execute();
    }
}
