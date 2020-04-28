<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\Stats;

use App\Component\BaseComponent;
use App\Database\Entity\EmailCampaign;
use App\Database\Repository\ContactRepository;
use App\Database\Repository\SentEmailRepository;

class Stats extends BaseComponent
{
    /**
     * @var ContactRepository
     */
    protected ContactRepository $contactRepository;

    /**
     * @var SentEmailRepository
     */
    protected SentEmailRepository $sentEmailRepository;

    /**
     * @var EmailCampaign
     */
    protected EmailCampaign $activeEmailCampaign;

    /**
     * Stats constructor.
     * @param ContactRepository $contactRepository
     * @param SentEmailRepository $sentEmailRepository
     * @param EmailCampaign $activeEmailCampaign
     */
    public function __construct(
        ContactRepository $contactRepository,
        SentEmailRepository $sentEmailRepository,
        EmailCampaign $activeEmailCampaign
    ) {
        $this->contactRepository = $contactRepository;
        $this->sentEmailRepository = $sentEmailRepository;
        $this->activeEmailCampaign = $activeEmailCampaign;
    }

    /**
     * Render
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function render() : void
    {
        if ($this->activeEmailCampaign->getStatus() === EmailCampaign::STATUS_INACTIVE) {
            $contactCount = $this->contactRepository->countByEmailCampaignEmailNotNull($this->activeEmailCampaign);
        } else {
            $contactCount = $this->sentEmailRepository->countByEmailCampaign($this->activeEmailCampaign);
        }

        $sentEmailCount = $this->sentEmailRepository->countByEmailCampaignSentTimeNotNull($this->activeEmailCampaign);

        $this->template->render(__DIR__ . '/templates/stats.latte', [
            'emailCampaign' => $this->activeEmailCampaign,
            'contactCount' => $contactCount,
            'sentEmailCount' => $sentEmailCount
        ]);
    }
}
