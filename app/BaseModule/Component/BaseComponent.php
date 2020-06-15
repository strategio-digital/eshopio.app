<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Component;

use App\BaseModule\Presenter\AbstractPresenter;
use App\BaseModule\Presenter\TAbstractPresenter;
use Nette\Application\UI\Control;

/**
 * Class BaseComponent
 * @package App\Component
 * @property AbstractPresenter $presenter
 */
class BaseComponent extends Control
{
    use TAbstractPresenter;
}
