<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\BreadCrumb;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;

interface IBreadCrumb
{
    /**
     * @param array<BreadCrumbItem> $breadCrumb
     * @return BreadCrumb
     */
    public function create(array $breadCrumb) : BreadCrumb;
}
