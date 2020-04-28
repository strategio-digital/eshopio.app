<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\TestModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\EmailCampaign;
use App\Database\Entity\User;
use App\Database\Manager\EmailCampaignManager;
use App\Mailer\Campaign\CampaignMailer;
use Nette\Application\UI\Form;
use Nette\Mail\SmtpException;

class TestModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var EmailCampaignManager
     */
    protected EmailCampaignManager $emailCampaignManager;

    /**
     * @var CampaignMailer
     */
    protected CampaignMailer $campaignMailer;

    /**
     * @var EmailCampaign
     */
    protected EmailCampaign $activeEmailCampaign;

    /**
     * @var User
     */
    protected User $activeUser;

    /**
     * TestModal constructor.
     * @param EmailCampaignManager $emailCampaignManager
     * @param CampaignMailer $campaignMailer
     * @param EmailCampaign $activeEmailCampaign
     * @param User $activeUser
     */
    public function __construct(
        EmailCampaignManager $emailCampaignManager,
        CampaignMailer $campaignMailer,
        EmailCampaign $activeEmailCampaign,
        User $activeUser
    ) {
        $this->emailCampaignManager = $emailCampaignManager;
        $this->campaignMailer = $campaignMailer;
        $this->activeEmailCampaign = $activeEmailCampaign;
        $this->activeUser = $activeUser;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/test-modal.latte');
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = new Form;
        $form->addSubmit('send');

        $form->addText('email')
            ->setRequired('E-mail je povinný.')
            ->addRule(Form::EMAIL, 'E-mail je v neplatném formátu.')
            ->setDefaultValue($this->activeUser->getEmail());

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {


            $smtpSetting = $this->activeEmailCampaign->getSmtpSetting();
            if ($smtpSetting) {
                try {
                    $this->campaignMailer->configure($smtpSetting);
                    $this->campaignMailer->sendTest($this->activeEmailCampaign, $values->email);

                    $this->notification(Notification::SUCCESS, 'Test kampaně', "Na email '{$values->email}' byl úspěšně odeslán náhled kampaně ke kontrole.", 0, FALSE);
                    $this->toggleModal($this->getName(), 'hide');

                    $form->reset();
                    $form->setValues(['email' => $this->activeUser->getEmail()]);
                    $this->redrawControl('form');

                } catch (SmtpException $exception) {
                    $this->notification(Notification::DANGER, 'Test kampaně', $exception->getMessage(), 0, FALSE);
                    $this->notification(Notification::DANGER, 'Test kampaně', 'Přes přiřazený SMTP server se nepodařila odeslat zpráva.', 0);
                }
            } else {
                $this->notification(Notification::DANGER, 'Test kampaně', 'Kampaň nemá přiřazený SMTP server.', 0);
            }
        };

        return $form;
    }
}
