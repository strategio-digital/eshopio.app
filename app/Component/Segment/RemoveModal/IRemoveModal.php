<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\RemoveModal;

interface IRemoveModal
{
    /**
     * @return RemoveModal
     */
    public function create() : RemoveModal;
}
