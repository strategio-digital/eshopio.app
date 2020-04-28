<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\User\LoginForm;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Security\Authenticator;
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
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/login-form.latte');
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
                $this->presenter->redirect('Project:select');

            } catch (AuthenticationException $exception) {
                $this->notification(Notification::DANGER, 'Přihlášení', $exception->getMessage(), 3000);
            }
        };

        return $form;
    }
}
