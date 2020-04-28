<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Entity;

use App\Database\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nette\InvalidArgumentException;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"project_id", "email"}),
 *     @ORM\UniqueConstraint(columns={"project_id", "phone"}),
 *     @ORM\UniqueConstraint(columns={"project_id", "phone", "email"})
 * })
 */
class Contact extends Entity
{
    const PHONE_REGEX = '^\+[0-9\s]{6,}$';

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected ?int $id;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="contacts")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected Project $project;

    /**
     * @var Segment
     * @ORM\ManyToOne(targetEntity="Segment", inversedBy="contacts")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected ?Segment $segment = NULL;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="SentEmail", mappedBy="contact")
     */
    protected Collection $sentEmails;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     */
    protected ?string $email = NULL;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=TRUE)
     */
    protected ?string $phone = NULL;

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
     * Contact constructor.
     */
    public function __construct()
    {
        $this->sentEmails = new ArrayCollection;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project) : void
    {
        $this->project = $project;
    }

    /**
     * @param Segment $segment
     */
    public function setSegment(Segment $segment) : void
    {
        $this->segment = $segment;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException("Parameter \$name '{$name}' is not valid name.");
        }

        $this->name = $name;
    }

    /**
     * @param string|null $email
     * @throws InvalidArgumentException
     */
    public function setEmail(?string $email) : void
    {
        if ($email && !Validators::isEmail($email)) {
            throw new InvalidArgumentException("Parameter \$email '{$email}' is not valid email address.");
        }

        $this->email = $this->__toNull($email);
    }

    /**
     * @param string $phone
     */
    public function setPhone(?string $phone) : void
    {
        if ($phone && !(preg_match('/' . self::PHONE_REGEX . '/', $phone) === 1)) {
            throw new InvalidArgumentException("Parameter \$phone '{$phone}' is not valid email address.");
        }

        $this->phone = $this->__toNull($phone);
    }

    /**
     * Reach messages count
     */
    public function increaseMessagesCount() : void
    {
        $this->messagesCount += 1;
    }

    /**
     * @throws \Exception
     */
    public function setLastUsage() : void
    {
        $this->lastUsage = new \DateTime();
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
     * @return Segment|null
     */
    public function getSegment() : ?Segment
    {
        return $this->segment;
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
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhone() : ?string
    {
        return $this->phone;
    }

    /**
     * @return int
     */
    public function getMessagesCount() : int
    {
        return $this->messagesCount;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastUsage() : ?\DateTime
    {
        return $this->lastUsage;
    }

    /**
     * @return ArrayCollection|SentEmail[]
     */
    public function getSentEmails() : Collection
    {
        return $this->sentEmails;
    }
}
