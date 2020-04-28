<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\SmtpSetting\Summary;

use App\Database\Entity\Project;
use App\Database\Entity\User;

interface ISummary
{
    /**
     * @param Project $activeProject
     * @param User $activeUser
     * @return Summary
     */
    public function create(Project $activeProject, User $activeUser) : Summary;
}
