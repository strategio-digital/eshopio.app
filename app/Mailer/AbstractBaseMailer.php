<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer;

use App\Database\Entity\SmtpSetting;
use App\Database\Manager\SmtpSettingManager;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

abstract class AbstractBaseMailer
{
    /**
     * @var SmtpSettingManager
     */
    protected SmtpSettingManager $smtpSettingManager;

    /**
     * @var SmtpMailer
     */
    protected SmtpMailer $smtpMailer;

    /**
     * @var SmtpSetting
     */
    protected SmtpSetting $smtpSetting;

    /**
     * @var Message
     */
    protected Message $message;

    /**
     * SmtpTestMailer constructor.
     * @param SmtpSettingManager $smtpSettingManager
     */
    public function __construct(SmtpSettingManager $smtpSettingManager)
    {
        $this->smtpSettingManager = $smtpSettingManager;
        $this->message = new Message;
    }

    /**
     * @param SmtpSetting $smtpSetting
     * @param bool $persistentConnection
     */
    public function configure(SmtpSetting $smtpSetting, bool $persistentConnection = FALSE) : void
    {
        $config = [
            'host' => $smtpSetting->getHost(),
            'username' => $smtpSetting->getUsername(),
            'password' => $smtpSetting->getPassword(),
            'port' => $smtpSetting->getPort(),
        ];

        if ($smtpSetting->getSecure()) {
            $config['secure'] = $smtpSetting->getSecure();
        }

        if ($persistentConnection) {
            $config['persistent'] = TRUE;
        }

        $this->smtpMailer = new SmtpMailer($config);
        $this->smtpSetting = $smtpSetting;
        $this->message->setFrom($smtpSetting->getSenderEmail(), $smtpSetting->getSenderName());
    }
}
