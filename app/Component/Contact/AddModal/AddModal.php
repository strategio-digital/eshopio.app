<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\AddModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\Project;
use App\Database\Manager\ContactManager;
use App\Database\Repository\ContactRepository;
use App\Database\Repository\SegmentRepository;
use Nette\Application\UI\Form;

class AddModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SegmentRepository
     */
    protected SegmentRepository $segmentRepository;

    /**
     * @var ContactRepository
     */
    protected ContactRepository $contactRepository;

    /**
     * @var ContactManager
     */
    protected ContactManager $contactManager;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * AddModal constructor.
     * @param SegmentRepository $segmentRepository
     * @param ContactRepository $contactRepository
     * @param ContactManager $contactManager
     * @param Project $activeProject
     */
    public function __construct(
        SegmentRepository $segmentRepository,
        ContactRepository $contactRepository,
        ContactManager $contactManager,
        Project $activeProject
    ) {
        $this->segmentRepository = $segmentRepository;
        $this->contactRepository = $contactRepository;
        $this->contactManager = $contactManager;
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
        $segments = $this->segmentRepository->findAllByProject($this->activeProject);

        $segmentsByIds = [
            NULL => '---'
        ];

        foreach ($segments as $segment) {
            $segmentsByIds[$segment->getId()] = $segment->getName();
        }

        $form = new Form;

        $form->addText('name')
            ->setRequired('Název kontaktu je povinný.');

        $form->addSelect('segmentId')
            ->setItems($segmentsByIds)
            ->setRequired('Zařazení do segmentu je povinné.');

        // Email a telefon
        $email = $form->addText('email')->setRequired(FALSE);
        $phone = $form->addText('phone')->setRequired(FALSE);

        // Pokud je email nevyplněn, nastaví se telefon jako povinný, to platí obráceně a zároveň
        $email->addConditionOn($phone, Form::EQUAL, '')->setRequired('E-mail nebo telefon je povinný.');
        $phone->addConditionOn($email, Form::EQUAL, '')->setRequired('E-mail nebo telefon je povinný.');

        // Pokud je email vyplněn, aplikují se validační pravidla
        $email->addCondition(Form::FILLED, TRUE)
            ->addRule(Form::EMAIL, 'E-mail je v neplatném formátu.');

        // Pokud je telefon vyplněn, aplikují se validační pravidla
        $phone->addCondition(Form::FILLED, TRUE)
            ->addRule(Form::MAX_LENGTH, 'Maximálné délka tel. čísla je %d znaků.', 32)
            ->addRule(Form::PATTERN, 'Zadejte číslo s plus na začátku, př: +420 000 000 000.', '^\+[0-9\s]{6,}$')
            ->addFilter(function (string $value) {
                return str_replace(' ', '', $value);
            });

        $form->addSubmit('save');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            if ($values->email !== '') {
                if ($this->contactRepository->findOneByProjectAndEmail($this->activeProject, $values->email)) {
                    $form->getComponent('email')->addError("Kontakt s e-mailem '{$values->email}' v tomto projektu již existuje.");
                }
            }

            if ($values->phone !== '') {
                if ($this->contactRepository->findOneByProjectAndPhone($this->activeProject, $values->phone)) {
                    $form->getComponent('phone')->addError("Kontakt s tel. číslem '{$values->phone}' v tomto projektu již existuje.");
                }
            }
        };

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {

            $segment = $this->segmentRepository->findOneById($values->segmentId);

            try {
                if ($values->phone !== '' && $values->email !== '') {
                    $this->contactManager->create($this->activeProject, $segment, $values->email, $values->phone, $values->name);
                } else if ($values->phone !== '') {
                    $this->contactManager->createByPhone($this->activeProject, $segment, $values->phone, $values->name);
                } else if ($values->email !== '') {
                    $this->contactManager->createByEmail($this->activeProject, $segment, $values->email, $values->name);
                }
            } catch (\Exception $exception) {
                throw $exception;
            }

            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Kontakty', "Kontakt '{$values->name}' byl úspěšně přidán.", 4000, FALSE);

            // Reset form
            $form->reset();
            $this->redrawControl('form');

            // Redraw summary
            $this->presenter->getComponent('contactSummary')->redrawControl('summary');
        };

        return $form;
    }
}
