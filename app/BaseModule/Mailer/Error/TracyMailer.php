<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Mailer\Error;

use App\BaseModule\Mailer\AbstractAppMailer;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class TracyMailer extends AbstractAppMailer implements Mailer
{
    public function send(Message $message) : void
    {
        $message->addTo($this->senderEmail);
        parent::send($message);
    }
}