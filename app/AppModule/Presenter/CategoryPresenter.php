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

final class CategoryPresenter extends FrontendPresenter
{
    public function beforeRender()
    {
        parent::beforeRender();

        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Název kategorie', 'Category:summary')
        ]);

        $this->breadCrumbComponent->setBreadCrumbItems($breadCrumbItems);
    }
}