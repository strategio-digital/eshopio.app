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

class ArticlePresenter extends FrontendPresenter
{
    public function beforeRender()
    {
        parent::beforeRender();

        $this->breadCrumb->setBreadCrumbItems(new ArrayCollection([
            //new BreadCrumbItem('Články', NULL),
            new BreadCrumbItem('Název článku', ':App:Article:detail')
        ]));
    }
}