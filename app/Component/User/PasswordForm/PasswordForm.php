<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\User\PasswordForm;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\User;
use App\Database\Manager\UserManager;
use App\Security\Passwords;
use Nette\Application\UI\Form;

class PasswordForm extends BaseComponent
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
     * PasswordForm constructor.
     * @param UserManager $userManager
     * @param User $activeUser
     */
    public function __construct(UserManager $userManager, User $activeUser)
    {
        $this->userManager = $userManager;
        $this->activeUser = $activeUser;
    }

    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/password-form.latte');
    }

    protected function createComponentForm() : Form
    {
        $form = new Form;

        $form->addPassword('actualPassword')
            ->setRequired('Aktuální heslo je povinné.');

        $form->addPassword('newPassword')
            ->setRequired('Nové heslo je povinné.')
            ->addRule(Form::MIN_LENGTH, 'Minimální délka hesla je %d znaků.', 8);

        $form->addPassword('confirmPassword')
            ->setRequired('Potvrzení hesla je povinné.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['newPassword']);

        $form->addSubmit('save');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $verified = (new Passwords)->verify($values->actualPassword, $this->activeUser->getPassword());
            if (!$verified) {
                $form['actualPassword']->addError('Aktuální heslo není správné.');
            }
        };

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $this->userManager->updatePassword($this->activeUser, $values->newPassword);
            $form->reset();
            $this->notification(Notification::SUCCESS, 'Uživatel', 'Heslo bylo úspěšně upraveno.', 3000, FALSE);
            $this->redrawControl('form');
        };

        return $form;
    }
}
