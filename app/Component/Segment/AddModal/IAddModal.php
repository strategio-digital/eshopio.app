<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\AddModal;

use App\Database\Entity\Project;

interface IAddModal
{
    /**
     * @param Project $activeProject
     * @return AddModal
     */
    public function create(Project $activeProject) : AddModal;
}
