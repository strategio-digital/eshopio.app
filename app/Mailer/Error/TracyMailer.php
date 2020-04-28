<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer\Error;


use App\Mailer\AbstractAppMailer;
use Nette\Mail\IMailer;
use Nette\Mail\Message;

class TracyMailer extends AbstractAppMailer implements IMailer
{
    public function send(Message $message) : void
    {
        $message->addTo($this->senderEmail);
        parent::send($message);
    }
}