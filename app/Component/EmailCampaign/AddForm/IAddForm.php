<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\AddForm;

use App\Database\Entity\Project;

interface IAddForm
{
    /**
     * @param Project $activeProject
     * @return AddForm
     */
    public function create(Project $activeProject) : AddForm;
}
