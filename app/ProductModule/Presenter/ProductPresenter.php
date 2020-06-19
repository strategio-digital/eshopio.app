<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\ProductModule\Presenter;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\BaseModule\Presenter\FrontendPresenter;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPresenter extends FrontendPresenter
{
    public function actionDetail() : void
    {
        $breadCrumbItems = new ArrayCollection([
            new BreadCrumbItem('Kategorie', ':Category:Category:summary'),
            new BreadCrumbItem('Pod-kategorie', ':Category:Category:summary'),
            new BreadCrumbItem('Název produktu', ':Product:Product:detail')
        ]);

        $this->breadCrumb->setBreadCrumbItems($breadCrumbItems);
    }
}