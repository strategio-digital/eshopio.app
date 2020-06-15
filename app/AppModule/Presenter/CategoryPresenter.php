<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\AppModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\BaseModule\Presenter\BasePresenter;
use Doctrine\Common\Collections\ArrayCollection;

final class CategoryPresenter extends BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        $this->setLayout(__DIR__ . '/templates/@frontend.latte');

        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Název kategorie', 'Category:summary')
        ]);

        $this->breadCrumbComponent->setBreadCrumbItems($breadCrumbItems);
    }
}