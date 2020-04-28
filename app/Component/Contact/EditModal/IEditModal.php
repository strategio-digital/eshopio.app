<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\EditModal;

use App\Database\Entity\Project;

interface IEditModal
{
    /**
     * @param Project $activeProject
     * @return EditModal
     */
    public function create(Project $activeProject) : EditModal;
}
