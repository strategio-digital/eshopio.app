<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\AppModule\Presenter;

use App\BaseModule\Presenter\BasePresenter;

final class HomePresenter extends BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        $this->setLayout(__DIR__ . '/templates/@frontend.latte');
    }
}