<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 */
class Project
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $secretKey;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $name;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects")
     */
    protected Collection $users;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Segment", mappedBy="project")
     */
    protected Collection $segments;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="project")
     */
    protected Collection $contacts;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="SmtpSetting", mappedBy="project")
     */
    protected Collection $smtpSettings;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="EmailCampaign", mappedBy="project")
     */
    protected Collection $emailCampaigns;

    /**
     * Project constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection;
        $this->segments = new ArrayCollection;
        $this->smtpSettings = new ArrayCollection;
        $this->contacts = new ArrayCollection;
        $this->emailCampaigns = new ArrayCollection;
    }

    /**
     * @throws \Exception
     */
    public function setSecretKey() : void
    {
        $this->secretKey = Uuid::uuid4()->toString();
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
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSecretKey() : string
    {
        return $this->secretKey;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers() : Collection
    {
        return $this->users;
    }

    /**
     * @return ArrayCollection|Segment[]
     */
    public function getSegments() : Collection
    {
        return $this->segments;
    }

    /**
     * @return ArrayCollection|SmtpSetting[]
     */
    public function getSmtpSettings() : Collection
    {
        return $this->smtpSettings;
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
