<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\TestModal;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\User;

interface ITestModal
{
    /**
     * @param User $activeUser
     * @param EmailCampaign $activeEmailCampaign
     * @return TestModal
     */
    public function create(User $activeUser, EmailCampaign $activeEmailCampaign) : TestModal;
}