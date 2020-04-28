<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer\SmtpTest;

use App\Mailer\AbstractBaseMailer;

class SmtpTestMailer extends AbstractBaseMailer
{
    /**
     * @param string $to
     * @throws \Exception
     */
    public function send(string $to) : void
    {
        $this->message->addTo($to);
        $this->message->setSubject('Test spojení se SMTP | Contactio');
        $this->message->setBody(
            "Test spojení se SMTP byl úspěšně proveden.\n\r\n\r" .
            "E-mail odesílatele: {$this->smtpSetting->getSenderEmail()}\n\r" .
            "Jméno odesílatele: {$this->smtpSetting->getSenderName()}\n\r" .
            "Server: {$this->smtpSetting->getHost()}\n\r" .
            "Port: {$this->smtpSetting->getPort()}" . ($this->smtpSetting->getSecure() ? ' / ' . strtoupper($this->smtpSetting->getSecure()) : '') . "\n\r" .
            "Heslo: *******"
        );

        $this->smtpMailer->send($this->message);
        $this->smtpSettingManager->increaseLimits($this->smtpSetting);
    }
}