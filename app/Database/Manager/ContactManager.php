<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\Contact;
use App\Database\Entity\Project;
use App\Database\Entity\Segment;

class ContactManager extends BaseManager
{
    public function createByEmail(Project $project, Segment $segment, string $email, string $name) : Contact
    {
        $contact = new Contact;
        $contact->setProject($project);
        $contact->setSegment($segment);
        $contact->setEmail($email);
        $contact->setName($name);

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    public function createByPhone(Project $project, Segment $segment, string $phone, string $name) : Contact
    {
        $contact = new Contact;
        $contact->setProject($project);
        $contact->setSegment($segment);
        $contact->setPhone($phone);
        $contact->setName($name);

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    public function create(Project $project, Segment $segment, string $email, string $phone, string $name) : Contact
    {
        $contact = new Contact;
        $contact->setProject($project);
        $contact->setSegment($segment);
        $contact->setEmail($email);
        $contact->setPhone($phone);
        $contact->setName($name);

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    /**
     * @param Contact $contact
     * @param Segment $segment
     * @param string $name
     * @return Contact
     */
    public function update(Contact $contact, Segment $segment, string $name) : Contact
    {
        $contact->setSegment($segment);
        $contact->setName($name);

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    /**
     * @param Contact $contact
     * @param string $email
     * @return Contact
     */
    public function updateEmail(Contact $contact, string $email) : Contact
    {
        $contact->setEmail($email);

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    /**
     * @param Contact $contact
     * @param string $phone
     * @return Contact
     */
    public function updatePhone(Contact $contact, string $phone) : Contact
    {
        $contact->setPhone($phone);

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }

    /**
     * @param Contact $contact
     * @return Contact
     * @throws \Exception
     */
    public function updateStats(Contact $contact) : Contact
    {
        $contact->setLastUsage();
        $contact->increaseMessagesCount();

        $this->em->persist($contact);
        $this->em->flush();

        return $contact;
    }


    /**
     * @param Contact $contact
     */
    public function remove(Contact $contact) : void
    {
        $this->em->remove($contact);
        $this->em->flush();
    }
}
