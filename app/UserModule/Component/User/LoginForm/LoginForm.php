<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Component\User\LoginForm;

use App\BaseModule\Component\BaseComponent;
use App\BaseModule\Component\FormDisplayError;
use App\BaseModule\Component\Notification\Entity\Notification;
use App\UserModule\Security\Authenticator;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;

class LoginForm extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var Authenticator
     */
    protected Authenticator $authenticator;

    /**
     * @var User
     */
    protected User $user;

    /**
     * LoginForm constructor.
     * @param Authenticator $authenticator
     * @param User $user
     */
    public function __construct(Authenticator $authenticator, User $user)
    {
        $this->authenticator = $authenticator;
        $this->user = $user;
    }

    /**
     * Render classic Form
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/login-form.latte');
    }

    /**
     * Render DropDown form
     */
    public function renderDropdown() : void
    {
        $this->template->render(__DIR__ . '/templates/login-dropdown.latte');
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = new Form;

        $form->addText('email')
            ->addRule(Form::EMAIL, 'Zadejte platnou e-mailovou adresu')
            ->setRequired('E-mailová adresa je povinná.');

        $form->addPassword('password')->setRequired('Heslo je povinné.');
        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            try {

                $identity = $this->authenticator->authenticate([$values->email, $values->password]);
                $this->user->login($identity);

                $this->notificationFlash(Notification::SUCCESS, 'Přihlášení', 'Přihlášení proběhlo v pořádku', 3000);
                $this->presenter->redirect(':User:User:profile');

            } catch (AuthenticationException $exception) {
                bdump('yep');
                $this->notification(Notification::DANGER, 'Přihlášení', $exception->getMessage(), 3000, TRUE);
            }
        };

        return $form;
    }
}