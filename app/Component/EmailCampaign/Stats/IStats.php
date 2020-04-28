<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\Stats;

use App\Database\Entity\EmailCampaign;

interface IStats
{
    /**
     * @param EmailCampaign $activeEmailCampaign
     * @return Stats
     */
    public function create(EmailCampaign $activeEmailCampaign) : Stats;
}
