<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
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
}
