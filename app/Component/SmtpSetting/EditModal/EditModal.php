<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\SmtpSetting\EditModal;

use App\Component\Notification\Entity\Notification;
use App\Component\SmtpSetting\AddModal\AddModal;
use App\Database\Manager\SmtpSettingManager;
use App\Database\Repository\SmtpSettingRepository;
use Nette\Application\UI\Form;

class EditModal extends AddModal
{
    /**
     * @var SmtpSettingRepository
     */
    protected SmtpSettingRepository $smtpSettingRepository;

    /**
     * @var int|null
     * @persistent
     */
    public ?int $id = NULL;

    /**
     * EditModal constructor.
     * @param SmtpSettingRepository $smtpSettingRepository
     * @param SmtpSettingManager $smtpSettingManager
     */
    public function __construct(SmtpSettingRepository $smtpSettingRepository, SmtpSettingManager $smtpSettingManager)
    {
        $this->smtpSettingRepository = $smtpSettingRepository;
        $this->smtpSettingManager = $smtpSettingManager;
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = parent::createComponentForm();

        if ($this->id)
        {
            $smtpSetting = $this->smtpSettingRepository->findOneById($this->id);
            $form->addHidden('id')->addRule(Form::INTEGER);
            $form->getComponent('password')->setRequired(FALSE);

            $form->setDefaults([
                'id' => $smtpSetting->getId(),
                'senderEmail' => $smtpSetting->getSenderEmail(),
                'senderName' => $smtpSetting->getSenderName(),
                'host' => $smtpSetting->getHost(),
                'username' => $smtpSetting->getUsername(),
                'password' => NULL,
                'secure' => $smtpSetting->getSecure(),
                'port' => $smtpSetting->getPort(),
                'minuteLimit' => $smtpSetting->getMinuteLimit(),
                'dayLimit' => $smtpSetting->getDayLimit()
            ]);
        }

        $form->onSuccess[0] = function (Form $form, \stdClass $values) {
            // Update smtpSetting
            $smtpSetting = $this->smtpSettingRepository->findOneById($this->id);

            $this->smtpSettingManager->beginTransaction();

            try
            {
                $this->smtpSettingManager->update(
                    $smtpSetting,
                    $values->senderEmail,
                    $values->senderName,
                    $values->host,
                    $values->username,
                    $values->secure,
                    $values->port,
                    $values->minuteLimit,
                    $values->dayLimit,
                );

                if (trim($values->password) !== '') {
                    $this->smtpSettingManager->updatePassword($smtpSetting, $values->password);
                }

                $this->smtpSettingManager->commit();

            } catch (\Exception $exception) {
                $this->smtpSettingManager->rollback();;
            }

            // Toggle modal & Notification
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Nastavení SMTP', "Nastavení STMP serveru bylo upraveno.", 4000, FALSE);

            // Redraw smtpSettingSummary
            $this->presenter->getComponent('smtpSettingSummary')->redrawControl('summary');
        };

        return  $form;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleOpen(int $id) : void
    {
        $this->id = $id;
        $this->toggleModal($this->getName(), 'show', FALSE);
        $this->redrawControl('form');
    }
}
