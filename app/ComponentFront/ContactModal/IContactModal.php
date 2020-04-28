<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\ComponentFront\ContactModal;


interface IContactModal
{
    /**
     * @return ContactModal
     */
    public function create() : ContactModal;
}