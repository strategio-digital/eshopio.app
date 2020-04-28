<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\User\LoginForm\ILoginForm;
use App\Component\User\LoginForm\LoginForm;
use App\Component\User\NameForm\INameForm;
use App\Component\User\NameForm\NameForm;
use App\Component\User\PasswordForm\IPasswordForm;
use App\Component\User\PasswordForm\PasswordForm;

final class UserPresenter extends BasePresenter
{
    /**
     * @var ILoginForm
     * @inject
     */
    public ILoginForm $ILoginForm;

    /**
     * @var INameForm
     * @inject
     */
    public INameForm $INameForm;

    /**
     * @var IPasswordForm
     * @inject
     */
    public IPasswordForm $IPasswordForm;

    public function actionProfile() : void
    {
        $this->template->seoTitle = 'Váš profil';

        $this->breadCrumb[] = new BreadCrumbItem('Uživatel', NULL);
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    public function actionLogin() : void
    {
        $this->template->seoTitle = 'Přihlášení';

        $this->breadCrumb[] = new BreadCrumbItem('Uživatel', NULL);
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    protected function createComponentUserLoginForm() : LoginForm
    {
        return $this->ILoginForm->create();
    }

    protected function createComponentUserNameForm() : NameForm
    {
        return $this->INameForm->create($this->activeUser);
    }

    protected function createComponentUserPasswordForm() : PasswordForm
    {
        return $this->IPasswordForm->create($this->activeUser);
    }
}
