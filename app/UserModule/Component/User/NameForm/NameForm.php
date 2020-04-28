<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Component\User\NameForm;

use App\BaseModule\Component\BaseComponent;
use App\BaseModule\Component\FormDisplayError;
use App\BaseModule\Component\Notification\Entity\Notification;
use App\UserModule\Database\Entity\User;
use App\UserModule\Database\Manager\UserManager;
use Nette\Application\UI\Form;

class NameForm extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var UserManager
     */
    protected UserManager $userManager;

    /**
     * @var User
     */
    protected User $activeUser;

    /**
     * NameForm constructor.
     * @param UserManager $userManager
     * @param User $activeUser
     */
    public function __construct(UserManager $userManager, User $activeUser)
    {
        $this->activeUser = $activeUser;
        $this->userManager = $userManager;
    }

    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/name-form.latte');
    }

    public function createComponentForm() : Form
    {
        $form = new Form;

        $form->addEmail('email')
            ->setEmptyValue($this->activeUser->getEmail())
            ->setDisabled(TRUE);

        $form->addText('firstName')
            ->setDefaultValue($this->activeUser->getFirstName());

        $form->addText('lastName')
            ->setDefaultValue($this->activeUser->getLastName());

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->userManager->updateName($this->activeUser, $values->firstName, $values->lastName);
            $this->notification(Notification::SUCCESS, 'Uživatel', 'Uživatelské jméno a příjmení bylo upraveno.', 3000);
        };

        return $form;
    }
}
