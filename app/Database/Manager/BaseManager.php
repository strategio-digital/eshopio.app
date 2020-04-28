<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use Nettrine\ORM\EntityManagerDecorator;

abstract class BaseManager
{
    protected EntityManagerDecorator $em;

    /**
     * UserManager constructor.
     * @param EntityManagerDecorator $em
     */
    public function __construct(EntityManagerDecorator $em)
    {
        $this->em = $em;
    }

    public function beginTransaction() : void
    {
        $this->em->beginTransaction();
    }

    public function commit() : void
    {
        $this->em->commit();
    }

    public function rollback() : void
    {
        $this->em->rollBack();
    }
}
