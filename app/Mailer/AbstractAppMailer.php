<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer;

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

abstract class AbstractAppMailer
{
    /**
     * @var int
     */
    protected int $port = 465;

    /**
     * @var string
     */
    protected string $secure = 'ssl';

    /**
     * @var string
     */
    protected string $host;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $senderEmail;

    /**
     * @var string
     */
    protected string $senderName;

    /**
     * @var string
     */
    protected string $bccEmail;

    /**
     * @var string
     */
    protected string $domain;

    /**
     * AbstractAppMailer constructor.
     * @param string $host
     * @param string $password
     * @param string $senderEmail
     * @param string $senderName
     * @param string $bccEmail
     * @param string $domain
     * @param int|NULL $port
     * @param string|NULL $secure
     */
    public function __construct(
        string $host,
        string $password,
        string $senderEmail,
        string $senderName,
        string $bccEmail,
        string $domain,
        int $port = NULL,
        string $secure = NULL
    ) {
        $this->host = $host;
        $this->password = $password;
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->bccEmail = $bccEmail;
        $this->domain = $domain;

        $this->setPort($port);
        $this->setSecure($secure);
    }

    /**
     * @param int|null $port
     */
    public function setPort(?int $port) : void
    {
        if ($port){
            $this->port = $port;
        }
    }

    /**
     * @param string|null $secure
     */
    public function setSecure(?string $secure) : void
    {
        if ($secure) {
            $this->secure = $secure;
        }
    }

    /**
     * @param Message $message
     */
    protected function send(Message $message) : void
    {
        $config = [
            'host' => $this->host,
            'username' => $this->senderEmail,
            'password' => $this->password,
        ];

        if($this->secure){
            $config['secure'] = $this->secure;
        }

        if($this->port){
            $config['port'] = $this->port;
        }

        $message->addBcc($this->bccEmail);
        $message->setFrom($this->senderEmail, $this->senderName);
        $message->setPriority(1);

        $smtpMailer = new SmtpMailer($config);
        $smtpMailer->send($message);
    }


}
