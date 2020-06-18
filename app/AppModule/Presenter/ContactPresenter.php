<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\AppModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\BaseModule\Presenter\FrontendPresenter;
use Doctrine\Common\Collections\ArrayCollection;

final class ContactPresenter extends FrontendPresenter
{
    public function beforeRender()
    {
        parent::beforeRender();

        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Kontakty', ':App:Contact:summary')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }
}