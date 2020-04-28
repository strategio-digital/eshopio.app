<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Entity\Aggregation;

class Segment
{
    /**
     * @var int
     */
    protected int $contactsCount = 0;

    /**
     * @return int
     */
    public function getContactsCount() : int
    {
        return $this->contactsCount;
    }

    /**
     * @param int $contactsCount
     */
    public function setContactsCount(int $contactsCount) : void
    {
        $this->contactsCount = $contactsCount;
    }
}
