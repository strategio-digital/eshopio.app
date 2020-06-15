<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\AppModule\Component\BreadCrumb\Entity;


class BreadCrumbItem
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $link;

    /**
     * BreadCrumbItem constructor.
     * @param string $name
     * @param string $link
     */
    public function __construct(string $name, string $link)
    {
        $this->name = $name;
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink() : string
    {
        return $this->link;
    }
}