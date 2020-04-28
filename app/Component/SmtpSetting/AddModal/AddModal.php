<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\SmtpSetting\AddModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\Project;
use App\Database\Entity\SmtpSetting;
use App\Database\Manager\SmtpSettingManager;
use Nette\Application\UI\Form;

class AddModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SmtpSettingManager
     */
    protected SmtpSettingManager $smtpSettingManager;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * AddModal constructor.
     * @param SmtpSettingManager $smtpSettingManager
     * @param Project $activeProject
     */
    public function __construct(SmtpSettingManager $smtpSettingManager, Project $activeProject)
    {
        $this->smtpSettingManager = $smtpSettingManager;
        $this->activeProject = $activeProject;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/add-modal.latte');
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $secure = [
            SmtpSetting::NOT_SECURE => 'Bez zabezpečení',
            SmtpSetting::SECURE_SSL => 'SSL šifrování',
            SmtpSetting::SECURE_TLS => 'TLS šifrování'
        ];

        $form = new Form;

        $form->addText('senderEmail')
            ->addRule(Form::EMAIL, 'E-mail je v neplatném formátu.')
            ->setRequired('E-mail odesílatele je povinný.');

        $form->addText('senderName')
            ->setRequired('Jméno odesílatele je povinné.');

        $form->addText('host')
            ->setRequired('SMTP server je povinný.');

        $form->addText('username')
            ->setRequired('SMTP uživatel je povinný.');

        $form->addPassword('password')
            ->setRequired('SMTP heslo je povinné.');

        $form->addSelect('secure')
            ->setItems($secure)
            ->setRequired('Typ šifrování je povinný.');

        $form->addText('port')
            ->addRule(Form::INTEGER, 'Port musí být celé číslo.')
            ->setRequired('Port je povinný.');

        $form->addText('minuteLimit')
            ->addRule(Form::INTEGER, 'Minutový limit musí být celé číslo.')
            ->setRequired('Minutový limit je povinný');

        $form->addText('dayLimit')
            ->addRule(Form::INTEGER, 'Denní limit musí být celé číslo.')
            ->setRequired('Denní limit je povinný.');

        $form->addSubmit('save');

        $form->setDefaults([
            'secure' => SmtpSetting::SECURE_TLS,
            'port' => 578,
            'minuteLimit' => 5,
            'dayLimit' => 300
        ]);

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $smtpSetting = $this->smtpSettingManager->create(
                $this->activeProject,
                $values->senderEmail,
                $values->senderName,
                $values->host,
                $values->username,
                $values->password,
                $values->secure,
                $values->port,
                $values->minuteLimit,
                $values->dayLimit
            );

            // Toggle modal & add notification
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Nastavení SMTP', "SMTP server '{$smtpSetting->getHost()}' byl úspěšně přidán.", 4000, FALSE);

            // Reset form
            $form->reset();
            $this->redrawControl('form');

            // Redraw summary
            $this->presenter->getComponent('smtpSettingSummary')->redrawControl('summary');
        };

        return $form;
    }
}
