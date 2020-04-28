<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Component;

use App\BaseModule\Presenter\BasePresenter;
use App\BaseModule\Presenter\TBasePresenter;
use Nette\Application\UI\Control;

/**
 * Class BaseComponent
 * @package App\Component
 * @property BasePresenter $presenter
 */
class BaseComponent extends Control
{
    use TBasePresenter;
}
