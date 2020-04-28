<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\SmtpSetting\RemoveModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Manager\SmtpSettingManager;
use App\Database\Repository\SmtpSettingRepository;
use Nette\Application\UI\Form;

class RemoveModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SmtpSettingRepository
     */
    protected SmtpSettingRepository $smtpSettingRepository;

    /**
     * @var SmtpSettingManager
     */
    protected SmtpSettingManager $smtpSegmentManager;

    /**
     * @var int|null
     */
    public ?int $id = NULL;

    /**
     * RemoveModal constructor.
     * @param SmtpSettingRepository $smtpSettingRepository
     * @param SmtpSettingManager $smtpSegmentManager
     */
    public function __construct(SmtpSettingRepository $smtpSettingRepository, SmtpSettingManager $smtpSegmentManager)
    {
        $this->smtpSettingRepository = $smtpSettingRepository;
        $this->smtpSegmentManager = $smtpSegmentManager;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/remove-modal.latte');
    }

    protected function createComponentForm() : Form
    {
        $form = new Form();
        $form->addHidden('id')
            ->addRule(Form::INTEGER, 'Id SMTP serveru musí být celé číslo.')
            ->setRequired('Id SMTP serveru je povinné.')
            ->setDefaultValue($this->id);

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $smtpSetting = $this->smtpSettingRepository->findOneById($values->id);
            $this->smtpSegmentManager->remove($smtpSetting);

            $form->reset();
            $this->redrawControl('form');

            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Nastavení SMTP', "SMTP server '{$smtpSetting->getHost()}' byl úspěšně odstraněn.", 4000, FALSE);

            $this->presenter->getComponent('smtpSettingSummary')->redrawControl('summary');
        };

        return $form;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleOpen(int $id) : void
    {
        $this->id = $id;
        $this->toggleModal($this->getName(), 'show');
        $this->redrawControl('form');
    }
}
