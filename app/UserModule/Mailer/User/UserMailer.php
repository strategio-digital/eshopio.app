<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\UserModule\Mailer\User;

use App\BaseModule\Mailer\AbstractAppMailer;
use App\UserModule\Database\Entity\User;

class UserMailer extends AbstractAppMailer
{
    /**
     * @param User $user
     * @param string|null $newPassword
     */
    public function addedUser(User $user, ?string $newPassword) : void
    {
        /*$message = new Message;
        $message->addTo($user->getEmail());
        $message->setSubject("Přístup k projektu {$project->getName()}");
        $message->setBody(
            "Dobrý den,\n\r" .
            "Vaše e-mailová adresa byla přiřazena k projektu '{$project->getName()}'.\n\r" .
            "Pro správu projektu se prosím přihlašte na webu {$this->domain}." .
            ($newPassword ? "\n\rPo přihlášení si ve Vašem profilu nastavte nové heslo!\n\rAktuální (dočasné) heslo je: {$newPassword}" : '')
        );
        $this->send($message);*/
    }
}
