<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\ImportModal;

use App\Database\Entity\Project;

interface IImportModal
{
    /**
     * @param Project $activeProject
     * @return ImportModal
     */
    public function create(Project $activeProject) : ImportModal;
}
