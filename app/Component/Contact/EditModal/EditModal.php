<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\EditModal;

use App\Component\Notification\Entity\Notification;
use App\Component\Contact\AddModal\AddModal;
use App\Database\Entity\Project;
use App\Database\Manager\ContactManager;
use App\Database\Repository\ContactRepository;
use App\Database\Repository\SegmentRepository;
use Nette\Application\UI\Form;

class EditModal extends AddModal
{
    /**
     * @var ContactRepository
     */
    protected ContactRepository $contactRepository;

    /**
     * @var int|null
     * @persistent
     */
    public ?int $id = NULL;

    /**
     * EditModal constructor.
     * @param ContactRepository $contactRepository
     * @param SegmentRepository $segmentRepository
     * @param ContactManager $contactManager
     * @param Project $activeProject
     */
    public function __construct(
        ContactRepository $contactRepository,
        SegmentRepository $segmentRepository,
        ContactManager $contactManager,
        Project $activeProject
    ) {
        $this->contactRepository = $contactRepository;
        $this->segmentRepository = $segmentRepository;
        $this->contactManager = $contactManager;
        $this->activeProject = $activeProject;
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = parent::createComponentForm();

        if ($this->id)
        {
            $form->addHidden('id')->addRule(Form::INTEGER)->setRequired('Id Kontaktu je povinné.');
            $contact = $this->contactRepository->findOneById($this->id);

            $defaults = [
                'id' => $contact->getId(),
                'name' => $contact->getName(),
                'email' => $contact->getEmail(),
                'phone' => $contact->getPhone()
            ];

            if ($contact->getSegment()) {
                $defaults['segmentId'] = $contact->getSegment()->getId();
            }

            $form->setDefaults($defaults);
        }

        $form->onValidate[0] = function (Form $form, \stdClass $values) {

            $contactById = $this->contactRepository->findOneById($values->id);
            $contactByEmail = $values->email !== '' ? $this->contactRepository->findOneByProjectAndEmail($this->activeProject, $values->email) : NULL;
            $contactByPhone = $values->phone !== '' ? $this->contactRepository->findOneByProjectAndPhone($this->activeProject, $values->phone) : NULL;

            if ($contactByEmail && $contactByEmail->getId() !== $contactById->getId()) {
                $form->getComponent('email')->addError("Kontakt s e-mailem '{$values->email}' v tomto projektu již existuje.");
            }

            if ($contactByPhone && $contactByPhone->getId() !== $contactById->getId()) {
                $form->getComponent('phone')->addError("Kontakt s tel. číslem'{$values->phone}' v tomto projektu již existuje.");
            }

        };

        $form->onSuccess[0] = function (Form $form, \stdClass $values) {
            $contact = $this->contactRepository->findOneById($this->id);
            $segment = $this->segmentRepository->findOneById($values->segmentId);

            $this->contactManager->beginTransaction();

            try {
                $this->contactManager->update($contact, $segment, $values->name);

                $this->contactManager->updateEmail($contact, $values->email);
                $this->contactManager->updatePhone($contact, $values->phone);

                $this->contactManager->commit();

            } catch (\Exception $exception) {
                $this->contactManager->rollback();
                throw $exception;
            }

            // Toggle modal & Notification
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Kontakt', "Kontakt byl úspěšně upraven.", 4000, FALSE);

            // Redraw contactSummary
            $this->presenter->getComponent('contactSummary')->redrawControl('summary');
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
