<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\HomepageModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\BaseModule\Presenter\FrontendPresenter;
use Doctrine\Common\Collections\ArrayCollection;

final class HomepagePresenter extends FrontendPresenter
{
    public function startup() : void
    {
        parent::startup();

        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Název e-shopu', ':Homepage:Homepage:summary'),
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }
}