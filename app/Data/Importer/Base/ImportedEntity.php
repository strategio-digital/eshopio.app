<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Data\Importer\Base;

use App\Database\Entity\Contact;
use Nette\InvalidArgumentException;

class ImportedEntity
{
    protected Contact $contact;

    protected ?string $name = NULL;

    protected ?string $email = NULL;

    protected ?string $phone = NULL;

    protected array $exceptions = [];

    /**
     * importedEntity constructor.
     */
    public function __construct()
    {
        $this->contact = new Contact;
    }

    /**
     * @param string|null $email
     * @param string|null $phone
     */
    public function setEmailAndPhone(?string $email, ?string $phone) : void
    {
        if ($email || $phone) {
            if ($email) {
                try {
                    $this->contact->setEmail($email);
                } catch (InvalidArgumentException $exception) {
                    $this->addException("E-mail není ve správném formátu.");
                }
            }

            if ($phone) {
                $phone = str_replace(' ', '', $phone);
                try {
                    $this->contact->setPhone($phone);
                } catch (InvalidArgumentException $exception) {
                    $this->addException("Telefon není ve správném formátu.");
                }
            }
        } else {
            $this->addException("E-mail nebo telefon je povinný.");
        }

        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        if ($name) {
            try {
                $this->contact->setName($name);
            } catch (InvalidArgumentException $exception) {
                $this->addException("Název kontaktu není ve správném formátu.");
            }
        } else {
            $this->addException("Název kontaktu je povinný.");
        }

        $this->name = $name;
    }

    /**
     * @param string $message
     */
    public function addException(string $message) : void
    {
        $this->exceptions[] =  new BaseImporterException($message);
    }

    /**
     * @return string|null
     */
    public function getName() : ?string
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
     * @return BaseImporterException[]
     */
    public function getExceptions() : array
    {
        return $this->exceptions;
    }
}