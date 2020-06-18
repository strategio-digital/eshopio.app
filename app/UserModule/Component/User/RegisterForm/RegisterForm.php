<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Component\User\RegisterForm;


use App\BaseModule\Component\BaseComponent;

class RegisterForm extends BaseComponent
{
    public function render() : void
    {
        $this->template->render();
    }
}