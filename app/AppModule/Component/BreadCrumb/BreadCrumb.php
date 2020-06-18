<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\AppModule\Component\BreadCrumb;

use App\AppModule\Component\BreadCrumb\Entity\BreadCrumbItem;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Application\UI\Control;

class BreadCrumb extends Control
{
    /**
     * @var ArrayCollection|BreadCrumbItem[]
     */
    protected ArrayCollection $breadCrumbItems;

    /**
     * @param ArrayCollection|BreadCrumbItem[] $breadCrumbItems
     */
    public function setBreadCrumbItems(ArrayCollection $breadCrumbItems) : void
    {
        $this->breadCrumbItems = $breadCrumbItems;
    }

    /**
     * @return BreadCrumbItem
     */
    public function getLastBreadCrumbItem() : BreadCrumbItem
    {
        return $this->breadCrumbItems->last();
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/breadcrumb.latte', [
            'breadCrumbItems' => $this->breadCrumbItems
        ]);
    }
}