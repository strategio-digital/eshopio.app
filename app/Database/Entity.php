<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database;

/**
 * Custom EntityManager
 */
abstract class Entity
{
    public function __toNull(string $value) : ?string
    {
        return trim($value) === '' ? NULL : $value;
    }
}
