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
use Nette\InvalidArgumentException;

/**
 * @ORM\Entity
 */
class SmtpSetting
{
    const
        NOT_SECURE = 'not_secure',
        SECURE_SSL = 'ssl',
        SECURE_TLS = 'tls'
    ;

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected ?int $id;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="smtpSettings")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected Project $project;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $host;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $senderEmail;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $senderName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=TRUE)
     */
    protected ?string $secure = NULL;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected int $port;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected int $minuteLimit;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected int $dayLimit;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected int $reachedMinuteLimit = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected int $reachedDayLimit = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected int $messagesCount = 0;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected ?\DateTime $lastUsage = NULL;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="EmailCampaign", mappedBy="smtpSetting")
     */
    protected Collection $emailCampaigns;

    /**
     * SmtpSetting constructor.
     */
    public function __construct()
    {
        $this->emailCampaigns = new ArrayCollection;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project) : void
    {
        $this->project = $project;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host) : void
    {
        $this->host = $host;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail(string $senderEmail) : void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName) : void
    {
        $this->senderName = $senderName;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    /**
     * @param string|null $secure
     */
    public function setSecure(?string $secure) : void
    {
        if ($secure === self::NOT_SECURE) {
            $secure = NULL;
        }

        if ($secure && ($secure !== self::SECURE_TLS && $secure !== self::SECURE_SSL)) {
            throw new InvalidArgumentException("Parameter '\$secure' contains invalid argumet '{$secure}'.");
        }

        $this->secure = $secure;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port) : void
    {
        $this->port = $port;
    }

    /**
     * @param int $minuteLimit
     */
    public function setMinuteLimit(int $minuteLimit) : void
    {
        $this->minuteLimit = $minuteLimit;
    }

    /**
     * @param int $dayLimit
     */
    public function setDayLimit(int $dayLimit) : void
    {
        $this->dayLimit = $dayLimit;
    }

    /**
     * @throws \Exception
     */
    public function increaseLimits() : void
    {
        $this->reachedMinuteLimit += 1;
        $this->reachedDayLimit += 1;
        $this->messagesCount += 1;

        $this->setLastUsage();
    }

    /**
     * @throws \Exception
     */
    public function decreaseLimits() : void
    {
        $this->reachedMinuteLimit -= 1;
        $this->reachedDayLimit -= 1;
        $this->messagesCount -= 1;

        $this->setLastUsage();
    }

    /**
     * @throws \Exception
     */
    public function setLastUsage() : void
    {
        $this->lastUsage = new \DateTime('now');
    }

    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return Project
     */
    public function getProject() : Project
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getSenderEmail() : string
    {
        return $this->senderEmail;
    }

    /**
     * @return string
     */
    public function getSenderName() : string
    {
        return $this->senderName;
    }

    /**
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSecure() : ?string
    {
        return $this->secure;
    }

    /**
     * @return int
     */
    public function getPort() : int
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getMinuteLimit() : int
    {
        return $this->minuteLimit;
    }

    /**
     * @return int
     */
    public function getDayLimit() : int
    {
        return $this->dayLimit;
    }

    /**
     * @return int
     */
    public function getReachedMinuteLimit() : int
    {
        return $this->reachedMinuteLimit;
    }

    /**
     * @return int
     */
    public function getReachedDayLimit() : int
    {
        return $this->reachedDayLimit;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastUsage() : ?\DateTime
    {
        return $this->lastUsage;
    }

    /**
     * @return int
     */
    public function getMessagesCount() : int
    {
        return $this->messagesCount;
    }
}
