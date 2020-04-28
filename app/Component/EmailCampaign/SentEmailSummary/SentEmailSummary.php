<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\SentEmailSummary;

use App\Component\BaseComponent;
use App\Database\Entity\EmailCampaign;
use App\Database\Repository\ContactRepository;
use App\Database\Repository\SentEmailRepository;

class SentEmailSummary extends BaseComponent
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
     * SentEmailSummary constructor.
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
     */
    public function render() : void
    {
        if ($this->activeEmailCampaign->getStatus() === EmailCampaign::STATUS_INACTIVE) {
            $rows = $this->contactRepository->findByEmailCampaignEmailNotNull($this->activeEmailCampaign);
        } else {
            $rows = $this->sentEmailRepository->findAllByEmailCampaign($this->activeEmailCampaign);
        }

        $this->template->render(__DIR__ . '/templates/summary.latte', [
            'emailCampaign' => $this->activeEmailCampaign,
            'rows' => $rows,
        ]);
    }
}
