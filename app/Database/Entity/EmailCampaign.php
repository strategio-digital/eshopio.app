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
use Nette\InvalidArgumentException;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 */
class EmailCampaign
{
    /**
     * Regex for date: 1.1.2020 | 22.12.2020
     * Regex for time: 05:59
     */
    const
        REGEX_DATE = '([1-9]|1[0-9]|2[0-9]|3[0-1])\.([1-9]|1[0-2])\.([0-2]{1}[0-9]{3})',
        REGEX_TIME = '(0[0-9]|1[0-9]|2[0-3])(\:(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])){1}';

    /**
     * Campaign statuses
     */
    const
        STATUS_INACTIVE = 0, // User can make changes
        STATUS_ACTIVE = 1, // Campaign is in progress, user cannot make changes
        STATUS_PAUSED = 2, // Campaign is paused, user cannot make changes
        STATUS_FINISHED = 3; // Campaign is finished, user cannot make changes

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = NULL;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $secretKey;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $name;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=FALSE)
     */
    protected \DateTime $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="time", nullable=FALSE)
     */
    protected \DateTime $startTime;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    protected string $subject;

    /**
     * @var string
     * @ORM\Column(type="string", length=4096)
     */
    protected string $message;

    /**
     * @var float
     * @ORM\Column(type="float", options={"default" : 0})
     */
    protected float $openRate = 0.0;

    /**
     * @var float
     * @ORM\Column(type="float", options={"default" : 0})
     */
    protected float $clickRate = 0.0;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"default" : 0})
     */
    protected int $status = self::STATUS_INACTIVE;

    /**
     * @ORM\ManyToOne(targetEntity="SmtpSetting", inversedBy="emailCampaigns")
     * @ORM\JoinColumn(nullable=TRUE, onDelete="SET NULL")
     */
    protected ?SmtpSetting $smtpSetting = NULL;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="emailCampaigns")
     * @ORM\JoinColumn(nullable=FALSE, onDelete="CASCADE")
     */
    protected Project $project;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="AllowedDay", mappedBy="emailCampaigns")
     */
    protected Collection $allowedDays;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Segment", mappedBy="emailCampaigns")
     */
    protected Collection $segments;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="SentEmail", mappedBy="emailCampaign")
     */
    protected Collection $sentEmails;

    /**
     * EmailCampaign constructor.
     */
    public function __construct()
    {
        $this->segments = new ArrayCollection;
        $this->allowedDays = new ArrayCollection;
        $this->sentEmails = new ArrayCollection;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @throws \Exception
     */
    public function setSecretKey() : void
    {
        $this->secretKey = Uuid::uuid4()->toString();
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate) : void
    {
        $this->startDate = $startDate;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime) : void
    {
        $this->startTime = $startTime;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject) : void
    {
        $this->subject = $subject;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message) : void
    {
        $this->message = $message;
    }

    /**
     * @param float $openRate
     */
    public function setOpenRate(float $openRate) : void
    {
        $this->openRate = $openRate;
    }

    /**
     * @param float $clickRate
     */
    public function setClickRate(float $clickRate) : void
    {
        $this->clickRate = $clickRate;
    }

    /**
     * @param int $status
     * @throws InvalidArgumentException
     */
    public function setStatus(int $status) : void
    {
        $statuses = [
            self::STATUS_INACTIVE,
            self::STATUS_ACTIVE,
            self::STATUS_PAUSED,
            self::STATUS_FINISHED
        ];

        if (!in_array($status, $statuses)) {
            throw new InvalidArgumentException("Parameter \$status '{$status}' is not valid status, check EmailCampaign::STATUS_*");
        }

        $this->status = $status;
    }

    /**
     * @param SmtpSetting $smtpSetting
     */
    public function setSmtpSetting(SmtpSetting $smtpSetting) : void
    {
        $this->smtpSetting = $smtpSetting;
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
     * @return \DateTime
     */
    public function getStartDate() : \DateTime
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime() : \DateTime
    {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getSubject() : string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @return float
     */
    public function getOpenRate() : float
    {
        return $this->openRate;
    }

    /**
     * @return float
     */
    public function getClickRate() : float
    {
        return $this->clickRate;
    }

    /**
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * @return SmtpSetting|null
     */
    public function getSmtpSetting() : ?SmtpSetting
    {
        return $this->smtpSetting;
    }

    /**
     * @return Project
     */
    public function getProject() : Project
    {
        return $this->project;
    }

    /**
     * @return ArrayCollection|AllowedDay[]
     */
    public function getAllowedDays() : Collection
    {
        return $this->allowedDays;
    }

    /**
     * @return ArrayCollection|Segment[]
     */
    public function getSegments() : Collection
    {
        return $this->segments;
    }

    /**
     * @return ArrayCollection|SentEmail[]
     */
    public function getSentEmails() : Collection
    {
        return $this->sentEmails;
    }
}
