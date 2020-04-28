<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\SmtpSetting\Summary;

use App\Component\BaseComponent;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\Project;
use App\Database\Entity\User;
use App\Database\Repository\SmtpSettingRepository;
use App\Mailer\SmtpTest\SmtpTestMailer;

class Summary extends BaseComponent
{
    /**
     * @var SmtpSettingRepository
     */
    protected SmtpSettingRepository $smtpSettingRepository;

    /**
     * @var SmtpTestMailer
     */
    protected SmtpTestMailer $smtpTesterMailer;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * @var User
     */
    protected User $activeUser;

    /**
     * Summary constructor.
     * @param SmtpSettingRepository $smtpSettingRepository
     * @param SmtpTestMailer $smtpTestMailer
     * @param Project $activeProject
     * @param User $activeUser
     */
    public function __construct(
        SmtpSettingRepository $smtpSettingRepository,
        SmtpTestMailer $smtpTestMailer,
        Project $activeProject,
        User $activeUser
    ) {
        $this->smtpSettingRepository = $smtpSettingRepository;
        $this->smtpTesterMailer = $smtpTestMailer;
        $this->activeProject = $activeProject;
        $this->activeUser = $activeUser;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $smtpSettings = $this->smtpSettingRepository->findAllByProject($this->activeProject);

        $this->template->render(__DIR__ . '/templates/summary.latte', [
            'smtpSettings' => $smtpSettings
        ]);
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleTest(int $id) : void
    {
        $smtpSetting = $this->smtpSettingRepository->findOneById($id);
        $this->smtpTesterMailer->configure($smtpSetting);

        try {
            $email = $this->activeUser->getEmail();
            $this->smtpTesterMailer->send($email);
            $this->notification(Notification::SUCCESS, 'Spojení se SMTP', "Na email '{$email}' byla úspěšně odeslána testovací zpráva.", 0, FALSE);
        } catch (\Exception $exception) {
            $this->notification(Notification::DANGER, 'Chyba spojení SMTP', $exception->getMessage(), 0, FALSE);
            $this->notification(Notification::DANGER, 'Chyba spojení SMTP', "Spojení s Vašim SMTP se nepodařilo nastavit.", 0, FALSE);
        }

        $this->redrawControl('summary');
    }
}
