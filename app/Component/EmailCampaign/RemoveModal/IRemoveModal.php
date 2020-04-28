<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\RemoveModal;

use App\Database\Entity\EmailCampaign;

interface IRemoveModal
{
    /**
     * @param EmailCampaign $activeEmailCampaign
     * @return RemoveModal
     */
    public function create(EmailCampaign $activeEmailCampaign) : RemoveModal;
}
