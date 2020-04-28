<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Database\Entity;

use App\BaseModule\Database\Entity;
use App\UserModule\Security\Passwords;
use Doctrine\ORM\Mapping as ORM;
use Nette\InvalidArgumentException;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 */
class User extends Entity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @var string
     * @ORM\Column(unique=TRUE, type="string", length=255, nullable=FALSE)
     */
    protected string $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     */
    protected ?string $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=TRUE)
     */
    protected ?string $lastName;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=FALSE)
     */
    protected string $password;

    /**
     * @ORM\Column(type="datetime", nullable=TRUE)
     */
    protected ?\DateTime $lastLogin = NULL;

    /**
     * @param string $email
     * @throws InvalidArgumentException
     */
    public function setEmail(string $email) : void
    {
        if (!Validators::isEmail($email)) {
            throw new InvalidArgumentException("Parameter {$email} is not valid e-mail address");
        }

        $this->email = $email;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName) : void
    {
        $firstName = $this->__toNull($firstName);
        $this->firstName = $firstName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName) : void
    {
        $lastName = $this->__toNull($lastName);
        $this->lastName = $lastName;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) : void
    {
        $password = (new Passwords)->hash($password);
        $this->password = $password;
    }

    /**
     * @throws \Exception
     */
    public function setLastLogin() : void
    {
        $this->lastLogin = new \DateTime('now');
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
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getFirstName() : ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName() : ?string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin() : ?\DateTime
    {
        return $this->lastLogin;
    }
}