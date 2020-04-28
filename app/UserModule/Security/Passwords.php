<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Security;

use Nette\Security\Passwords as NettePasswords;

class Passwords extends NettePasswords
{
    public function __construct($algo = PASSWORD_ARGON2I, array $options = [])
    {
        parent::__construct($algo, $options);
    }
}
