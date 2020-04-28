<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\Summary;

use App\Database\Entity\Project;

interface ISummary
{
    /**
     * @param Project $activeProject
     * @return Summary
     */
    public function create(Project $activeProject) : Summary;
}
