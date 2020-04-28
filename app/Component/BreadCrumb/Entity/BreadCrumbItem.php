<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\BreadCrumb\Entity;

class BreadCrumbItem
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected ?string $link;

    /**
     * BreadCrumbItem constructor.
     * @param string $name
     * @param string|null $link
     */
    public function __construct(string $name, ?string $link)
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
     * @return string|null
     */
    public function getLink() : ?string
    {
        return $this->link;
    }
}
