<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\Project;
use App\Database\Entity\SmtpSetting;

class EmailCampaignManager extends BaseManager
{
    /**
     * @param string $name
     * @param string $subject
     * @param string $message
     * @param \DateTime $startDate
     * @param \DateTime $startTime
     * @param SmtpSetting $smtpSetting
     * @param Project $project
     * @return EmailCampaign
     * @throws \Exception
     */
    public function create(string $name, string $subject, string $message, \DateTime $startDate, \DateTime $startTime, SmtpSetting $smtpSetting, Project $project) : EmailCampaign
    {
        $emailCampaign = new EmailCampaign;
        $emailCampaign->setSecretKey();
        $emailCampaign->setName($name);
        $emailCampaign->setSubject($subject);
        $emailCampaign->setMessage($message);
        $emailCampaign->setStartDate($startDate);
        $emailCampaign->setStartTime($startTime);
        $emailCampaign->setSmtpSetting($smtpSetting);
        $emailCampaign->setStatus(EmailCampaign::STATUS_INACTIVE);
        $emailCampaign->setProject($project);

        $this->em->persist($emailCampaign);
        $this->em->flush();

        return $emailCampaign;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param string $name
     * @param string $subject
     * @param string $message
     * @param \DateTime $startDate
     * @param \DateTime $startTime
     * @param SmtpSetting $smtpSetting
     * @return EmailCampaign
     */
    public function update(EmailCampaign $emailCampaign, string $name, string $subject, string $message, \DateTime $startDate, \DateTime $startTime, SmtpSetting $smtpSetting) : EmailCampaign
    {
        $emailCampaign->setName($name);
        $emailCampaign->setSubject($subject);
        $emailCampaign->setMessage($message);
        $emailCampaign->setStartDate($startDate);
        $emailCampaign->setStartTime($startTime);
        $emailCampaign->setSmtpSetting($smtpSetting);

        $this->em->persist($emailCampaign);
        $this->em->flush();

        return $emailCampaign;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param int $status
     * @return EmailCampaign
     */
    public function updateStatus(EmailCampaign $emailCampaign, int $status) : EmailCampaign
    {
        $emailCampaign->setStatus($status);

        $this->em->persist($emailCampaign);
        $this->em->flush();

        return $emailCampaign;
    }

    /**
     * @param EmailCampaign $emailCampaign
     */
    public function remove(EmailCampaign $emailCampaign) : void
    {
        $this->em->remove($emailCampaign);
        $this->em->flush();
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param int $sentEmailCount
     * @param int $openEmailCount
     */
    public function updateOpenRate(EmailCampaign $emailCampaign, int $sentEmailCount, int $openEmailCount) : void
    {
        $openRate = $this->calculateRate($sentEmailCount, $openEmailCount);
        $emailCampaign->setOpenRate($openRate);

        $this->em->persist($emailCampaign);
        $this->em->flush();
    }

    public function updateClickRate(EmailCampaign $emailCampaign, int $sentEmailCount, int $clickEmailCount) : void
    {
        $clickRate = $this->calculateRate($sentEmailCount, $clickEmailCount);
        $emailCampaign->setClickRate($clickRate);

        $this->em->persist($emailCampaign);
        $this->em->flush();
    }

    /**
     * @param int $sentEmailCount
     * @param int $secondValue
     * @return float
     */
    protected function calculateRate(int $sentEmailCount, int $secondValue) : float
    {
        if ($sentEmailCount === 0) {
            return 0;
        }

        return ($secondValue / $sentEmailCount) * 100;
    }
}
