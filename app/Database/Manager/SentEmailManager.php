<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\Contact;
use App\Database\Entity\EmailCampaign;
use App\Database\Entity\SentEmail;
use Nette\Http\IRequest;

class SentEmailManager extends BaseManager
{
    /**
     * @param EmailCampaign $emailCampaign
     * @param Contact $contact
     * @return SentEmail
     * @throws \Exception
     */
    public function create(EmailCampaign $emailCampaign, Contact $contact) : SentEmail
    {
        $sentEmail = new SentEmail;
        $sentEmail->setSecretKey();
        $sentEmail->setEmailCampaign($emailCampaign);
        $sentEmail->setContact($contact);

        $this->em->persist($sentEmail);
        $this->em->flush();

        return $sentEmail;
    }

    /**
     * @param SentEmail $sentEmail
     * @return SentEmail
     * @throws \Exception
     */
    public function updateSentTime(SentEmail $sentEmail) : SentEmail
    {
        $sentEmail->setSentTime(new \DateTime);

        $this->em->persist($sentEmail);
        $this->em->flush();

        return $sentEmail;
    }

    /**
     * @param SentEmail $sentEmail
     * @param IRequest $IRequest
     * @return SentEmail
     * @throws \Exception
     */
    public function increaseOpenRate(SentEmail $sentEmail, IRequest $IRequest) : SentEmail
    {
        $sentEmail->increaseOpenCount();
        $sentEmail->setLastOpen(new \DateTime());

        $sentEmail->setLastDevice($IRequest->getHeader('User-Agent'));
        $sentEmail->setLastIP($IRequest->getRemoteAddress());

        $this->em->persist($sentEmail);
        $this->em->flush();

        return $sentEmail;
    }

    /**
     * @param SentEmail $sentEmail
     * @param IRequest $IRequest
     * @return SentEmail
     * @throws \Exception
     */
    public function increaseClickRate(SentEmail $sentEmail, IRequest $IRequest) : SentEmail
    {
        $dateTime = new \DateTime();

        $sentEmail->increaseClickCount();
        $sentEmail->setLastClick($dateTime);

        $sentEmail->setLastBrowser($IRequest->getHeader('User-Agent'));
        $sentEmail->setLastIP($IRequest->getRemoteAddress());

        if ($sentEmail->getOpenCount() === 0) {
            $sentEmail->setLastOpen($dateTime);
            $sentEmail->increaseOpenCount();
        }

        $this->em->persist($sentEmail);
        $this->em->flush();

        return $sentEmail;
    }
}
