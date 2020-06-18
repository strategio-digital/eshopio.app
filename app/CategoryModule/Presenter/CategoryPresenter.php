<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\CategoryModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\BaseModule\Presenter\FrontendPresenter;
use Doctrine\Common\Collections\ArrayCollection;

final class CategoryPresenter extends FrontendPresenter
{
    public function beforeRender()
    {
        parent::beforeRender();

        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Název kategorie', ':Category:Category:summary')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }
}