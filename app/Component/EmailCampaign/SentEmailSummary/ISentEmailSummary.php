<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\SentEmailSummary;

use App\Database\Entity\EmailCampaign;

interface ISentEmailSummary
{
    /**
     * @param EmailCampaign $activeEmailCampaign
     * @return SentEmailSummary
     */
    public function create(EmailCampaign $activeEmailCampaign) : SentEmailSummary;
}
