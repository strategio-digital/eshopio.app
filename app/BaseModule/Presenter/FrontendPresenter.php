<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Presenter;

use App\AppModule\Component\BreadCrumb\BreadCrumbComponent;
use App\BaseModule\Component\Navbar\Navbar;

abstract class FrontendPresenter extends AbstractPresenter
{
    /**
     * @var BreadCrumbComponent
     */
    protected BreadCrumbComponent $breadCrumbComponent;

    /**
     * Startup
     */
    public function startup() : void
    {
        parent::startup();
        $this->setLayout(__DIR__ . '/templates/@frontend.latte');
        $this->breadCrumbComponent = new BreadCrumbComponent;
    }

    /**
     * @return Navbar
     */
    protected function createComponentNavbar() : Navbar
    {
        return new Navbar;
    }

    /**
     * @return BreadCrumbComponent
     */
    protected function createComponentBreadCrumb() : BreadCrumbComponent
    {
        return $this->breadCrumbComponent;
    }
}