<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer\ContactModal;

use App\Mailer\AbstractAppMailer;
use Nette\Http\IRequest;
use Nette\Mail\Message;

class ContactModalMailer extends AbstractAppMailer
{
    /**
     * @param string $contact
     * @param string $ctaText
     * @param IRequest $IRequest
     * @param \DateTime $createdAt
     * @param int $id
     * @throws \Exception
     */
    public function sentContactForm(string $contact, string $ctaText, IRequest $IRequest, \DateTime $createdAt, int $id) : void
    {
        $message = new Message;
        $message->addTo($this->bccEmail);
        $message->setSubject("Poptávka na oslovení přes Contactio");

        $message->setBody(
            "Dobrý den,\n\r" .
            "na webu {$this->domain} byl zanechán kontakt: {$contact}\n\r\n\r" .

            "CTA text: " . $ctaText . "\n\r" .
            "Datum a čas: " . (new \DateTime())->format('j.n.Y H:i:s') . "\n\r" .
            "IP Adresa: " . $IRequest->getRemoteAddress() . "\n\r" .
            "Conversion ID: " . $id
        );

        $this->send($message);
    }

    /**
     * @param string $email
     */
    public function sentRegistrationForm(string $email) : void
    {
        $message = new Message;
        $message->addTo($email);
        $message->setSubject("Dokončená registrace na Contactiu");

        $message->setBody(
            "Dobrý den,\n\r" .
            "pod Vaším e-mailem {$email} byla právě provedena registrace.\n\r\n\r" .
            "Pro rychlé seznámení s aplikací si můžete přehrát krátké video na webu {$this->domain}.\n\r".
            "Přihlašovací tlačítko naleznete v pravém horním rohu webu.\n\r\n\r" .
            "Doufáme, že Vám naše aplikace dobře poslouží.\n\r".
            "V případě jakýchkoli otázek jsme tu pro Vás."
        );

        $this->send($message);
    }
}