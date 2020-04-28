<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AllowedDay
{
    const
        MONDAY = 'PO',
        TUESDAY = 'ÚT',
        WEDNESDAY = 'ST',
        THURSDAY = 'ČT',
        FRIDAY = 'PÁ',
        SATURDAY = 'SO',
        SUNDAY = 'NE';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\Column(unique=TRUE, type="string", length=3, nullable=FALSE)
     */
    protected string $name;

    /**
     * @ORM\ManyToMany(targetEntity="EmailCampaign", inversedBy="allowedDays")
     */
    protected Collection $emailCampaigns;

    /**
     * AllowedDay constructor.
     */
    public function __construct()
    {
        $this->emailCampaigns = new ArrayCollection;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|EmailCampaign[]
     */
    public function getEmailCampaigns() : Collection
    {
        return $this->emailCampaigns;
    }

}
