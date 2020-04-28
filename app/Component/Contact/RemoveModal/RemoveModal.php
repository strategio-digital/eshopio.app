<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\RemoveModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Manager\ContactManager;
use App\Database\Repository\ContactRepository;
use Nette\Application\UI\Form;

class RemoveModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var ContactRepository
     */
    protected ContactRepository $contactRepository;

    /**
     * @var ContactManager
     */
    protected ContactManager $contactManager;

    /**
     * @var int|null
     */
    public ?int $id = NULL;

    /**
     * RemoveModal constructor.
     * @param ContactRepository $contactRepository
     * @param ContactManager $contactManager
     */
    public function __construct(ContactRepository $contactRepository, ContactManager $contactManager)
    {
        $this->contactRepository = $contactRepository;
        $this->contactManager = $contactManager;
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
        $form = new Form;
        $form->addHidden('id')
            ->addRule(Form::INTEGER, 'Id kontaktu musí být celé číslo.')
            ->setRequired('Id kontaktu je povinné.')
            ->setDefaultValue($this->id);

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $contact = $this->contactRepository->findOneById($values->id);
            $this->contactManager->remove($contact);

            $form->reset();
            $this->redrawControl('form');

            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Kontakty', "Kontakt '{$contact->getName()}' byl úspěšně odstraněn.", 4000, FALSE);

            $this->presenter->getComponent('contactSummary')->redrawControl('summary');
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
