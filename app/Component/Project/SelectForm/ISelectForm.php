<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\SelectForm;

use App\Database\Entity\User;

interface ISelectForm
{
    /**
     * @param User $activeUser
     * @return SelectForm
     */
    public function create(User $activeUser) : SelectForm;
}
