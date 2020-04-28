<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"email_campaign_id", "contact_id"}),
 *     },
 *     indexes={
 *          @ORM\Index(columns={"secret_key"})
 *     }
 * )
 */
class SentEmail
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @var EmailCampaign
     * @ORM\ManyToOne(targetEntity="EmailCampaign", inversedBy="sentEmails")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=FALSE)
     */
    protected EmailCampaign $emailCampaign;

    /**
     * @var Contact
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="sentEmails")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=TRUE)
     */
    protected ?Contact $contact;

    /**
     * @var string
     * @ORM\Column(type="string", length=36, nullable=FALSE)
     */
    protected string $secretKey;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected ?\DateTime $sentTime = NULL;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected ?\DateTime $lastOpen = NULL;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected ?\DateTime $lastClick = NULL;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected int $openCount = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    protected int $clickCount = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     */
    protected ?string $lastBrowser = NULL;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     */
    protected ?string $lastDevice = NULL;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=TRUE)
     */
    protected ?string $lastIP = NULL;

    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @param EmailCampaign $emailCampaign
     */
    public function setEmailCampaign(EmailCampaign $emailCampaign) : void
    {
        $this->emailCampaign = $emailCampaign;
    }

    /**
     * @param Contact $contact
     */
    public function setContact(Contact $contact) : void
    {
        $this->contact = $contact;
    }

    /**
     * @throws \Exception
     */
    public function setSecretKey() : void
    {
        $this->secretKey = Uuid::uuid4()->toString();
    }

    /**
     * @param \DateTime $sentTime
     */
    public function setSentTime(\DateTime $sentTime) : void
    {
        $this->sentTime = $sentTime;
    }

    /**
     * @param \DateTime $lastOpen
     */
    public function setLastOpen(\DateTime $lastOpen) : void
    {
        $this->lastOpen = $lastOpen;
    }

    /**
     * @param \DateTime $lastClick
     */
    public function setLastClick(\DateTime $lastClick) : void
    {
        $this->lastClick = $lastClick;
    }

    /**
     * @param string $lastBrowser
     */
    public function setLastBrowser(string $lastBrowser) : void
    {
        $this->lastBrowser = $lastBrowser;
    }

    /**
     * @param string $lastDevice
     */
    public function setLastDevice(string $lastDevice) : void
    {
        $this->lastDevice = $lastDevice;
    }

    /**
     * @param string $lastIP
     */
    public function setLastIP(string $lastIP) : void
    {
        $this->lastIP = $lastIP;
    }

    /**
     * Open count++
     */
    public function increaseOpenCount() : void
    {
        $this->openCount += 1;
    }

    /**
     * Click count++
     */
    public function increaseClickCount() : void
    {
        $this->clickCount += 1;
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return EmailCampaign
     */
    public function getEmailCampaign() : EmailCampaign
    {
        return $this->emailCampaign;
    }

    /**
     * @return Contact|null
     */
    public function getContact() : ?Contact
    {
        return $this->contact;
    }

    /**
     * @return string
     */
    public function getSecretKey() : string
    {
        return $this->secretKey;
    }

    /**
     * @return \DateTime|null
     */
    public function getSentTime() : ?\DateTime
    {
        return $this->sentTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastOpen() : ?\DateTime
    {
        return $this->lastOpen;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastClick() : ?\DateTime
    {
        return $this->lastClick;
    }

    /**
     * @return int
     */
    public function getOpenCount() : int
    {
        return $this->openCount;
    }

    /**
     * @return int
     */
    public function getClickCount() : int
    {
        return $this->clickCount;
    }

    /**
     * @return string|null
     */
    public function getLastBrowser() : ?string
    {
        return $this->lastBrowser;
    }

    /**
     * @return string|null
     */
    public function getLastDevice() : ?string
    {
        return $this->lastDevice;
    }

    /**
     * @return string|null
     */
    public function getLastIP() : ?string
    {
        return $this->lastIP;
    }
}