<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component;

use App\Presenter\BasePresenter;
use App\Presenter\TBasePresenter;
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
