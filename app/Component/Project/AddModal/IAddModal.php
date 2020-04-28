<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\AddModal;

use App\Database\Entity\User;

interface IAddModal
{
    /**
     * @param User $activeUser
     * @return AddModal
     */
    public function create(User $activeUser) : AddModal;
}
