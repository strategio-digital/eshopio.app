<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;


use App\Database\Entity\EmailCampaign;
use App\Database\Repository\SentEmailRepository;

class RateManager extends BaseManager
{
    /**
     * @var SentEmailRepository
     */
    protected SentEmailRepository $sentEmailRepository;

    /**
     * @var EmailCampaignManager
     */
    protected EmailCampaignManager $emailCampaignManager;

    /**
     * RateManager constructor.
     * @param SentEmailRepository $sentEmailRepository
     * @param EmailCampaignManager $emailCampaignManager
     */
    public function __construct(SentEmailRepository $sentEmailRepository, EmailCampaignManager $emailCampaignManager)
    {
        $this->sentEmailRepository = $sentEmailRepository;
        $this->emailCampaignManager = $emailCampaignManager;
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param int $sentEmailCount
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateOpenRate(EmailCampaign $emailCampaign, int $sentEmailCount) : void
    {
        $openEmailCount = $this->sentEmailRepository->countByEmailCampaignOpenCountIsNotZero($emailCampaign);
        $this->emailCampaignManager->updateOpenRate($emailCampaign, $sentEmailCount, $openEmailCount);
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param int $sentEmailCount
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updateClickRate(EmailCampaign $emailCampaign, int $sentEmailCount) : void
    {
        $clickEmailCount = $this->sentEmailRepository->countByEmailCampaignClickCountIsNotZero($emailCampaign);
        $this->emailCampaignManager->updateClickRate($emailCampaign, $sentEmailCount, $clickEmailCount);
    }
}