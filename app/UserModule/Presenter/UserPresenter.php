<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\BaseModule\Component\Notification\Entity\Notification;
use App\BaseModule\Presenter\FrontendPresenter;
use Doctrine\Common\Collections\ArrayCollection;

final class UserPresenter extends FrontendPresenter
{
    public function actionRegister() : void
    {
        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Registrace', ':User:User:register')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }

    public function actionPasswordRenew() : void
    {
        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Obnova hesla', ':User:User:register')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }

    public function actionPasswordUpdate() : void
    {
        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Nastavení nového hesla', ':User:User:register')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function actionProfile() : void
    {
        if (!$this->user->isLoggedIn()) {
            $this->notificationFlash(Notification::DANGER, 'Upozornění', 'Pro přístup do uživatelského profilu se musíte přihlásit.', 3000);
            $this->redirect(':Homepage:Homepage:summary');

        }

        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem("Nastavení účtu", ':User:User:profile')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }
}
