<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\ComponentFront\ContactModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Database\Manager\UserManager;
use App\Database\Repository\UserRepository;
use App\Mailer\ContactModal\ContactModalMailer;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class ContactModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;

    /**
     * @var UserManager
     */
    protected UserManager $userManager;

    /**
     * @var ContactModalMailer
     */
    protected ContactModalMailer $contactModalMailer;

    /**
     * ContactModal constructor.
     * @param UserRepository $userRepository
     * @param UserManager $userManager
     * @param ContactModalMailer $contactModalMailer
     */
    public function __construct(UserRepository $userRepository, UserManager $userManager, ContactModalMailer $contactModalMailer)
    {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->contactModalMailer = $contactModalMailer;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/contact-modal.latte');
    }

    /**
     * @return Form
     */
    protected function createComponentContactForm() : Form
    {
        $form = new Form;
        $form->addText('contact')->setRequired('Telefon / E-mail je povinný.');
        $form->addHidden('cta');
        $form->addSubmit('send');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $createdAt = new \DateTime;
            $id = $createdAt->getTimestamp();
            $request = $this->getPresenter()->getHttpRequest();

            $this->contactModalMailer->sentContactForm($values->contact, $values->cta, $request, $createdAt, $id);

            $this->presenter->payload->conversion = [
                'name' => 'contactForm',
                'id' => $id,
                'value' => 1.0,
                'currency' => 'CZK'
            ];
        };

        $form->onSuccess[] = function (Form $form) {
            $form->reset();
            $this->redrawControl('contactForm'); // it sends payload too
        };

        return $form;
    }

    /**
     * @return Form
     */
    protected function createComponentRegisterForm() : Form
    {
        $form = new Form;

        $form->addText('email')
            ->setRequired('Váš e-mail je povinný.')
            ->addRule(Form::EMAIL, 'E-mail je v neplatném formátu.');

        $form->addPassword('password')
            ->setRequired('Heslo je povinné.')
            ->addRule(Form::MIN_LENGTH, 'Minimální délka hesla je %d znaků.', 6)
            ->addRule(Form::PATTERN, 'Heslo musí obsahovat alespoň jedno písmeno.', '.*[a-žA-Ž]+.*')
            ->addRule(Form::PATTERN, 'Heslo musí obsahovat alespoň jednu číslici.', '.*[0-9]+.*');

        $form->addPassword('checkPassword')
            ->setRequired('Kontrola hesla je povinná.')
            ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form->getComponent('password'));

        $form->addSubmit('save');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $user = $this->userRepository->findOneByEmail($values->email);
            if ($user) {
                $form->getComponent('email')
                    ->addError("Uživatel s e-mailem: {$user->getEmail()} již existuje - stačí se přihlásit. Pokud jste zapomněli heslo, kontaktujte nás na telefon nebo e-mail.");

                $form->setValues([
                    'password' => NULL,
                    'checkPassword' => NULL
                ]);
            }
        };

        $form->onError[] = function (Form $form) {
            $this->redrawControl('registerForm');
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $user = $this->userManager->create($values->email, $values->password);

            try {
                $this->contactModalMailer->sentRegistrationForm($user->getEmail());
            } catch (\Exception $exception) {
                Debugger::log($exception);
            }

            $this->presenter->payload->conversion = [
                'name' => 'registerForm',
                'id' => $user->getId(),
                'value' => 1.0,
                'currency' => 'CZK'
            ];
        };

        $form->onSuccess[] = function (Form $form) {
            $this->presenter->sendPayload();
        };

        return $form;
    }
}