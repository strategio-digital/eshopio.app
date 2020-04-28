<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\AddModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\User;
use App\Database\Manager\ProjectManager;
use Nette\Application\UI\Form;

class AddModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var ProjectManager
     */
    protected ProjectManager $projectManager;

    /**
     * @var User
     */
    protected User $activeUser;

    /**
     * AddModal constructor.
     * @param ProjectManager $projectManager
     * @param User $activeUser
     */
    public function __construct(ProjectManager $projectManager, User $activeUser)
    {
        $this->projectManager = $projectManager;
        $this->activeUser = $activeUser;
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
        $form = new Form;

        $form->addText('name')
            ->setRequired('Název projektu je povinný.');

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            // Create project
            $this->projectManager->create($this->activeUser, $values->name);

            // Reset Form, toggle modal, notification
            $form->reset();
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Projekt', "Projekt '{$values->name}' byl úspěšně vytvořen.", 4000, FALSE);
            $this->redrawControl('form');

            // Redraw projectSummary
            $this->presenter->getComponent('projectSummary')->redrawControl('summary');
        };

        return $form;
    }
}
