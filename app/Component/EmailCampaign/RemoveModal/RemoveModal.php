<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\RemoveModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\EmailCampaign;
use App\Database\Manager\EmailCampaignManager;
use Nette\Application\UI\Form;

class RemoveModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var EmailCampaignManager
     */
    protected EmailCampaignManager $emailCampaignManager;

    /**
     * @var EmailCampaign
     */
    protected EmailCampaign $activeEmailCampaign;

    /**
     * RemoveModal constructor.
     * @param EmailCampaignManager $emailCampaignManager
     * @param EmailCampaign $activeEmailCampaign
     */
    public function __construct(EmailCampaignManager $emailCampaignManager, EmailCampaign $activeEmailCampaign)
    {
        $this->emailCampaignManager = $emailCampaignManager;
        $this->activeEmailCampaign = $activeEmailCampaign;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/remove-modal.latte');
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = new Form;
        $form->addSubmit('save');

        $form->addHidden('id')
            ->addRule(Form::INTEGER)
            ->setRequired(TRUE);

        $form->addHidden('secretKey')
            ->setRequired(TRUE);

        $form->setDefaults([
            'id' => $this->activeEmailCampaign->getId(),
            'secretKey' => $this->activeEmailCampaign->getSecretKey()
        ]);

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            if ($values->id === $this->activeEmailCampaign->getId() && $values->secretKey === $this->activeEmailCampaign->getSecretKey()) {
                $this->emailCampaignManager->remove($this->activeEmailCampaign);
                $this->notificationFlash(Notification::SUCCESS, 'E-mailová kampaň', "E-mailová kampaň '{$this->activeEmailCampaign->getName()}' byla odstraněna.");
                $this->presenter->redirect('EmailCampaign:summary');
            }
        };

        return $form;
    }
}
