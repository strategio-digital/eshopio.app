<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Presenter;

use Nette;

final class Error4xxPresenter extends AbstractPresenter
{
    public function startup() : void
    {
        parent::startup();
        if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
            $this->error();
        }
    }


    public function renderDefault(Nette\Application\BadRequestException $exception) : void
    {
        // load template 403.latte or 404.latte or ... 4xx.latte
        $file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
        $file = is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte';
        $this->template->setFile($file);
    }
}