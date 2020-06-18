<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Presenter;

use App\AppModule\Component\BreadCrumb\BreadCrumb;
use App\UserModule\Component\DropdownProfile\DropdownProfile;
use App\UserModule\Component\DropdownProfile\IDropDownProfile;
use App\UserModule\Component\LoginForm\ILoginForm;
use App\UserModule\Component\LoginForm\LoginForm;

abstract class FrontendPresenter extends AbstractPresenter
{
    /**
     * @var BreadCrumb
     */
    public BreadCrumb $breadCrumb;

    /**
     * @var ILoginForm
     * @inject
     */
    public ILoginForm $ILoginForm;

    /**
     * @var IDropDownProfile
     * @inject
     */
    public IDropDownProfile $IDropdownProfile;

    /**
     * Startup
     */
    public function startup() : void
    {
        parent::startup();
        $this->setLayout(__DIR__ . '/templates/@frontend.latte');
        $this->breadCrumb = new BreadCrumb;
    }

    /**
     * @return BreadCrumb
     */
    protected function createComponentBreadCrumb() : BreadCrumb
    {
        return $this->breadCrumb;
    }

    /**
     * @return LoginForm
     */
    protected function createComponentUserLoginForm() : LoginForm
    {
        return $this->ILoginForm->create();
    }

    /**
     * @return DropdownProfile
     */
    protected function createComponentUserDropdownProfile() : DropdownProfile
    {
        return $this->IDropdownProfile->create();
    }
}