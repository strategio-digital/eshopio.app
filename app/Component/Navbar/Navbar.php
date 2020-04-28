<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Navbar;

use App\Component\BaseComponent;

class Navbar extends BaseComponent
{
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/navbar.latte');
    }
}
