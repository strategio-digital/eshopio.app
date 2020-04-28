<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\AddForm;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\EmailCampaign;
use App\Database\Entity\Project;
use App\Database\Manager\EmailCampaignManager;
use App\Database\Manager\AllowedDayManager;
use App\Database\Manager\SegmentManager;
use App\Database\Repository\AllowedDayRepository;
use App\Database\Repository\SegmentRepository;
use App\Database\Repository\SmtpSettingRepository;
use Nette\Application\UI\Form;

class AddForm extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SmtpSettingRepository
     */
    protected SmtpSettingRepository $smtpSettingRepository;

    /**
     * @var AllowedDayRepository
     */
    protected AllowedDayRepository $allowedDayRepository;

    /**
     * @var SegmentRepository
     */
    protected SegmentRepository $segmentRepository;

    /**
     * @var EmailCampaignManager
     */
    protected EmailCampaignManager $emailCampaignManager;

    /**
     * @var AllowedDayManager
     */
    protected AllowedDayManager $allowedDayManager;

    /**
     * @var SegmentManager
     */
    protected SegmentManager $segmentManager;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * AddForm constructor.
     * @param SmtpSettingRepository $smtpSettingRepository
     * @param AllowedDayRepository $allowedDayRepository
     * @param SegmentRepository $segmentRepository
     * @param EmailCampaignManager $emailCampaignManager
     * @param AllowedDayManager $allowedDayManager
     * @param SegmentManager $segmentManager
     * @param Project $activeProject
     */
    public function __construct(
        SmtpSettingRepository $smtpSettingRepository,
        AllowedDayRepository $allowedDayRepository,
        SegmentRepository $segmentRepository,
        EmailCampaignManager $emailCampaignManager,
        AllowedDayManager $allowedDayManager,
        SegmentManager $segmentManager,
        Project $activeProject
    ) {
        $this->smtpSettingRepository = $smtpSettingRepository;
        $this->allowedDayRepository = $allowedDayRepository;
        $this->segmentRepository = $segmentRepository;
        $this->emailCampaignManager = $emailCampaignManager;
        $this->allowedDayManager = $allowedDayManager;
        $this->segmentManager = $segmentManager;
        $this->activeProject = $activeProject;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/add-form.latte');
    }

    /**
     * @return Form
     * @throws \Exception
     */
    protected function createComponentForm() : Form
    {
        $smtpSettings = $allowedDays = $segments = [];

        foreach ($this->smtpSettingRepository->findAllByProject($this->activeProject) as $smtpSetting) {
            $secure = $smtpSetting->getSecure() ? strtoupper($smtpSetting->getSecure()) : '';
            $smtpSettings[$smtpSetting->getId()] =  $smtpSetting->getSenderName() . ' <'
                . $smtpSetting->getSenderEmail() . '> | ' . $smtpSetting->getHost() . ' | '
                . $smtpSetting->getPort() . ' ' . $secure . ' | ' . $smtpSetting->getDayLimit() . ' zpráv / den';
        }

        foreach ($this->allowedDayRepository->findAll() as $allowedDay) {
            $allowedDays[$allowedDay->getId()] = $allowedDay->getName();
        }

        foreach ($this->segmentRepository->findAllByProjectEmailNotNull($this->activeProject) as $segment) {
            $segments[$segment->getId()] = $segment->getName() . ' <small>(počet adres: ' . $segment->getContactsCount() . ')</small>';
        }

        $form = new Form;

        $form->addText('name')
            ->setRequired('Název kampaně je povinný.');

        $form->addSelect('smtpSettingId')
            ->setRequired('SMTP server je povinný.')
            ->setItems($smtpSettings);

        $form->addText('startDate')
            ->setRequired('Datum první rozesílky je povinný.')
            ->addRule(Form::PATTERN, 'Datum musí být ve formátu: d.m.yyyy.', EmailCampaign::REGEX_DATE);

        $form->addText('startTime')
            ->setRequired('Čas odesílání je povinný.')
            ->addRule(Form::PATTERN, 'Čas musí být ve formátu: hh:mm.', EmailCampaign::REGEX_TIME);

        $form->addText('subject')
            ->setRequired("Předmět je povinný.")
            ->addRule(Form::MAX_LENGTH, 'Maximální délka předmětu je %d znaků.', 128);

        $form->addTextArea('message')
            ->addRule(Form::MAX_LENGTH, 'Maximální délka zprávy je %d znaků.', 4096)
            ->setRequired('Zpráva je povinná.');

        $form->addCheckboxList('allowedDayIds')
            ->setRequired('Minimálně jeden povolený den je povinný.')
            ->setItems($allowedDays);

        $form->addCheckboxList('segmentIds')
            ->setRequired('Minimálně jeden segment je povinný.')
            ->setItems($segments);

        $form->addSubmit('save');

        $form->setDefaults([
            'startDate' => (new \DateTime())->modify('+1 day')->format('j.n.Y'),
            'startTime' => '10:00',
            'message' => 'Dobrý den, ...'
        ]);

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $startDateTime = \DateTime::createFromFormat('j.n.YH:i:s', $values->startDate . $values->startTime . ':00');
            if ($startDateTime <= new \DateTime) {
                $form->addError("Datum a čas rozesílky nemůže být v minulosti.");
            }
        };

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $startDate = \DateTime::createFromFormat('j.n.Y H:i:s', $values->startDate . '00:00:00');
            $startTime = \DateTime::createFromFormat('d.m.Y H:i:s', '01.01.0000' . $values->startTime . ':00');

            $smtpSetting = $this->smtpSettingRepository->findOneById($values->smtpSettingId);
            $allowedDays = $this->allowedDayRepository->findByIdArray($values->allowedDayIds);
            $segments = $this->segmentRepository->findByIdArray($values->segmentIds);

            $this->emailCampaignManager->beginTransaction();
            try {
                $emailCampaign = $this->emailCampaignManager->create($values->name, $values->subject, $values->message, $startDate, $startTime, $smtpSetting, $this->activeProject);

                foreach ($allowedDays as $allowedDay) {
                    $this->allowedDayManager->appendEmailCampaign($allowedDay, $emailCampaign);
                }

                foreach ($segments as $segment) {
                    $this->segmentManager->appendEmailCampaign($segment, $emailCampaign);
                }

                $this->emailCampaignManager->commit();
            } catch (\Exception $exception) {
                $this->emailCampaignManager->rollback();
                throw $exception;
            }

            $this->notificationFlash(Notification::SUCCESS, 'E-mailová kampaň', "E-mailová kampaň '{$emailCampaign->getName()}' byla úspěšně vytvořena.");

            $this->presenter->redirect('EmailCampaign:detail', [
                'id' => $emailCampaign->getId(),
                'secretKey' => $emailCampaign->getSecretKey()
            ]);
        };

        return $form;
    }
}
