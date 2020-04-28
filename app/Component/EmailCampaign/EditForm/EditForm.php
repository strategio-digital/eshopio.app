<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\EditForm;

use App\Component\EmailCampaign\AddForm\AddForm;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\EmailCampaign;
use App\Database\Entity\Project;
use App\Database\Manager\EmailCampaignManager;
use App\Database\Manager\AllowedDayManager;
use App\Database\Manager\SegmentManager;
use App\Database\Manager\SentEmailManager;
use App\Database\Repository\ContactRepository;
use App\Database\Repository\AllowedDayRepository;
use App\Database\Repository\SegmentRepository;
use App\Database\Repository\SmtpSettingRepository;
use Nette\Application\UI\Form;


class EditForm extends AddForm
{
    /**
     * @var SentEmailManager
     */
    protected SentEmailManager $sentEmailManager;

    /**
     * @var ContactRepository
     */
    protected ContactRepository $contactRepository;

    /**
     * @var EmailCampaign
     */
    protected EmailCampaign $activeEmailCampaign;

    /**
     * EditForm constructor.
     * @param SmtpSettingRepository $smtpSettingRepository
     * @param AllowedDayRepository $allowedDayRepository
     * @param SegmentRepository $segmentRepository
     * @param ContactRepository $contactRepository
     * @param EmailCampaignManager $emailCampaignManager
     * @param AllowedDayManager $allowedDayManager
     * @param SegmentManager $segmentManager
     * @param SentEmailManager $sentEmailManager
     * @param Project $activeProject
     * @param EmailCampaign $activeEmailCampaign
     */
    public function __construct(
        SmtpSettingRepository $smtpSettingRepository,
        AllowedDayRepository $allowedDayRepository,
        SegmentRepository $segmentRepository,
        ContactRepository $contactRepository,
        EmailCampaignManager $emailCampaignManager,
        AllowedDayManager $allowedDayManager,
        SegmentManager $segmentManager,
        SentEmailManager $sentEmailManager,
        Project $activeProject,
        EmailCampaign $activeEmailCampaign
    ) {
        parent::__construct(
            $smtpSettingRepository,
            $allowedDayRepository,
            $segmentRepository,
            $emailCampaignManager,
            $allowedDayManager,
            $segmentManager,
            $activeProject
        );

        $this->contactRepository = $contactRepository;
        $this->sentEmailManager = $sentEmailManager;
        $this->activeEmailCampaign = $activeEmailCampaign;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/edit-form.latte', [
            'emailCampaign' => $this->activeEmailCampaign
        ]);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    protected function createComponentForm() : Form
    {
        $form =  parent::createComponentForm();
        $status = $this->activeEmailCampaign->getStatus();

        $allowedDayIds = $segmentIds = [];

        foreach ($this->activeEmailCampaign->getAllowedDays() as $allowedDay) {
            $allowedDayIds[] = $allowedDay->getId();
        }

        $dbSegmentIds = [];
        foreach ($this->segmentRepository->findAllByProjectEmailNotNull($this->activeProject) as $segment) {
            $dbSegmentIds[] = $segment->getId();
        }

        foreach ($this->activeEmailCampaign->getSegments() as $segment) {
            if (in_array($segment->getId(), $dbSegmentIds)) {
                $segmentIds[] = $segment->getId();
            }
        }

        if ($status === EmailCampaign::STATUS_PAUSED) {
            $form->getComponent('startDate')->setDisabled(TRUE);
        }

        if ($status === EmailCampaign::STATUS_FINISHED || $status === EmailCampaign::STATUS_ACTIVE) {
            foreach ($form->getComponents() as $component) {
                $component->setDisabled(TRUE);
            }
        }

        if ($status !== EmailCampaign::STATUS_INACTIVE) {
            $form->getComponent('segmentIds')->setDisabled(TRUE);
        }

        $smtpSetting = $this->activeEmailCampaign->getSmtpSetting();

        if (!$smtpSetting) {
            $component = $form->getComponent('smtpSettingId');
            $component->setItems([
                NULL => ''
            ] + $component->getItems());
        }

        $form->setDefaults([
            'name' => $this->activeEmailCampaign->getName(),
            'smtpSettingId' => $smtpSetting ? $smtpSetting->getId() : NULL,
            'startDate' => $this->activeEmailCampaign->getStartDate()->format('j.n.Y'),
            'startTime' => $this->activeEmailCampaign->getStartTime()->format('H:i'),
            'subject' => $this->activeEmailCampaign->getSubject(),
            'message' => $this->activeEmailCampaign->getMessage(),
            'allowedDayIds' => $allowedDayIds,
            'segmentIds' => $segmentIds
        ]);

        $form->onValidate[0] = function (Form $form, \stdClass $values) {
            $status = $this->activeEmailCampaign->getStatus();

            // Validuj datum a čas - pouze pokud kampaň ještě nebyla spuštěna
            if ($status === EmailCampaign::STATUS_INACTIVE) {
                $startDateTime = \DateTime::createFromFormat('j.n.YH:i:s', $values->startDate . $values->startTime . ':00');
                if ($startDateTime <= new \DateTime) {
                    $form->addError("Datum a čas rozesílky nemůže být v minulosti.");
                }
            }
        };

        $form->onSuccess[0] = function (Form $form, \stdClass $values) {
            $status = $this->activeEmailCampaign->getStatus();

            $startTime = \DateTime::createFromFormat('d.m.Y H:i:s', '01.01.0000' . $values->startTime . ':00');
            $smtpSetting = $this->smtpSettingRepository->findOneById($values->smtpSettingId);
            $allowedDays = $this->allowedDayRepository->findByIdArray($values->allowedDayIds);

            // Upravuj hodnoty pouze pokud kampaň ještě nebyla spuštěna, nebo je pauznutá
            if ($status === EmailCampaign::STATUS_INACTIVE) {
                $startDate = \DateTime::createFromFormat('j.n.Y H:i:s', $values->startDate . '00:00:00');
                $segments = $this->segmentRepository->findByIdArray($values->segmentIds);

                $this->emailCampaignManager->beginTransaction();
                try {
                    $this->emailCampaignManager->update($this->activeEmailCampaign, $values->name, $values->subject, $values->message, $startDate, $startTime, $smtpSetting);
                    $this->segmentManager->updateSegments($segments, $this->activeEmailCampaign);
                    $this->allowedDayManager->updateAllowedDays($allowedDays, $this->activeEmailCampaign);
                    $this->emailCampaignManager->commit();
                } catch (\Exception $exception) {
                    $this->emailCampaignManager->rollback();
                    throw $exception;
                }
            }

            if ($status === EmailCampaign::STATUS_PAUSED) {
                $startTime = \DateTime::createFromFormat('d.m.Y H:i:s', '01.01.0000' . $values->startTime . ':00');
                $smtpSetting = $this->smtpSettingRepository->findOneById($values->smtpSettingId);
                $startDate = $this->activeEmailCampaign->getStartDate();

                $this->emailCampaignManager->beginTransaction();
                try {
                    $this->emailCampaignManager->update($this->activeEmailCampaign, $values->name, $values->subject, $values->message, $startDate, $startTime, $smtpSetting);
                    $this->allowedDayManager->updateAllowedDays($allowedDays, $this->activeEmailCampaign);
                    $this->emailCampaignManager->commit();
                } catch (\Exception $exception) {
                    $this->emailCampaignManager->rollback();
                    throw $exception;
                }
            }

            $this->notification(Notification::SUCCESS, 'E-mailová kampaň', "Nastavení kampaně bylo uloženo.", 4000, FALSE);
            $this->presenter->getComponent('emailCampaignStats')->redrawControl('stats');
            $this->presenter->getComponent('emailCampaignSentEmailSummary')->redrawControl('summary');
        };

        return $form;
    }

    /**
     * Change campaign status
     * @param int $status
     * @throws \Exception
     */
    public function handleSetStatus(int $status) : void
    {
        $exceptions = [];
        $actualStatus = $this->activeEmailCampaign->getStatus();

        if (!($actualStatus < $status || ($actualStatus === EmailCampaign::STATUS_PAUSED && $status === EmailCampaign::STATUS_ACTIVE))) {
            $exceptions[] = 'Nelze nastavit požadovaný status.';
        }

        if (count($exceptions) === 0 && $actualStatus === EmailCampaign::STATUS_INACTIVE && $status === EmailCampaign::STATUS_ACTIVE) {
            $startDate = $this->activeEmailCampaign->getStartDate()->format('j.n.Y');
            $startTime = $this->activeEmailCampaign->getStartTime()->format('H:i:s');
            $startDateTime = \DateTime::createFromFormat('j.n.YH:i:s', $startDate . $startTime);

            if ($startDateTime <= new \DateTime) {
                $exceptions[] = 'Datum a čas rozesílky nemůže být v minulosti.';
            }

            if(count($exceptions) === 0) {
                $contacts = $this->contactRepository->findByEmailCampaignEmailNotNull($this->activeEmailCampaign);

                $this->sentEmailManager->beginTransaction();
                try {
                    foreach ($contacts as $contact) {
                        $this->sentEmailManager->create($this->activeEmailCampaign, $contact);
                    }
                    $this->sentEmailManager->commit();
                } catch (\Exception $exception) {
                    $this->sentEmailManager->rollback();
                    throw $exception;
                }
            }
        }

        if (count($exceptions) !== 0) {
            foreach ($exceptions as $exception) {
                $this->notification(Notification::DANGER, 'Emailová kampaň', $exception, 4000, FALSE);
            }
        } else {
            $this->emailCampaignManager->updateStatus($this->activeEmailCampaign, $status);
            $this->notification(Notification::SUCCESS, 'E-mailová kampaň', "Status kampaně byl právě upraven.", 4000, FALSE);
        }

        // Redraw form
        $this->redrawControl('form');

        // Redraw stats & result
        $this->presenter->getComponent('emailCampaignStats')->redrawControl('stats');
        $this->presenter->getComponent('emailCampaignSentEmailSummary')->redrawControl('summary');
    }
}
