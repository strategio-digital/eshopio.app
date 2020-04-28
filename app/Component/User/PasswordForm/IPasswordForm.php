<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\User\PasswordForm;

use App\Database\Entity\User;

interface IPasswordForm
{
    /**
     * @param User $activeUser
     * @return PasswordForm
     */
    public function create(User $activeUser) : PasswordForm;
}
