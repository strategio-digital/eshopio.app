<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\CollaboratorModal;

use App\Database\Entity\User;

interface ICollaboratorModal
{
    /**
     * @param User $activeUser
     * @return CollaboratorModal
     */
    public function create(User $activeUser) : CollaboratorModal;
}
