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
class Segment extends \App\Database\Entity\Aggregation\Segment
{
    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected ?int $id;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="segments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected Project $project;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $name;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="segment")
     */
    protected Collection $contacts;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="EmailCampaign", inversedBy="allowedDays")
     */
    protected Collection $emailCampaigns;

    /**
     * Segment constructor.
     */
    public function __construct()
    {
        $this->contacts = new ArrayCollection;
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
     * @param Project $project
     */
    public function setProject(Project $project) : void
    {
        $this->project = $project;
    }

    /**
     * @return int
     */
    public function getId() : int
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
     * @return Project
     */
    public function getProject() : Project
    {
        return $this->project;
    }

    /**
     * @return ArrayCollection|Contact[]
     */
    public function getContacts() : Collection
    {
        return $this->contacts;
    }

    /**
     * @return ArrayCollection|EmailCampaign[]
     */
    public function getEmailCampaigns() : Collection
    {
        return $this->emailCampaigns;
    }
}
