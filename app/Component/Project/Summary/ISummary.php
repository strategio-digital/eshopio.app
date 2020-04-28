<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\Summary;

use App\Database\Entity\Project;
use App\Database\Entity\User;

interface ISummary
{
    /**
     * @param User $activeUser
     * @param Project|null $activeProject
     * @return Summary
     */
    public function create(User $activeUser, ?Project $activeProject) : Summary;
}
