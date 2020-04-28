<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Component\User\NameForm;

use App\UserModule\Database\Entity\User;

interface INameForm
{
    /**
     * @param User $activeUser
     * @return NameForm
     */
    public function create(User $activeUser) : NameForm;
}
