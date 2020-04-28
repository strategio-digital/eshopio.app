<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\BreadCrumb;

use App\Component\BaseComponent;
use App\Component\BreadCrumb\Entity\BreadCrumbItem;

class BreadCrumb extends BaseComponent
{
    /**
     * @var array<BreadCrumbItem>
     */
    protected array $breadCrumb = [];

    /**
     * BreadCrumb constructor.
     * @param array<BreadCrumbItem> $breadCrumb
     */
    public function __construct(array $breadCrumb)
    {
        $this->breadCrumb = $breadCrumb;
    }

    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/bread-crumb.latte', [
            'breadCrumb' => $this->breadCrumb
        ]);
    }
}
