<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Mailer\User;

use App\Database\Entity\Project;
use App\Database\Entity\User;
use App\Mailer\AbstractAppMailer;
use Nette\Mail\Message;

class UserMailer extends AbstractAppMailer
{
    /**
     * @param Project $project
     * @param User $user
     * @param string|null $newPassword
     */
    public function addedProjectUser(Project $project, User $user, ?string $newPassword) : void
    {
        $message = new Message;
        $message->addTo($user->getEmail());
        $message->setSubject("Přístup k projektu {$project->getName()}");
        $message->setBody(
            "Dobrý den,\n\r" .
            "Vaše e-mailová adresa byla přiřazena k projektu '{$project->getName()}'.\n\r" .
            "Pro správu projektu se prosím přihlašte na webu {$this->domain}." .
            ($newPassword ? "\n\rPo přihlášení si ve Vašem profilu nastavte nové heslo!\n\rAktuální (dočasné) heslo je: {$newPassword}" : '')
        );
        $this->send($message);
    }

    /**
     * @param Project $project
     * @param User $user
     */
    public function removedProjectUser(Project $project, User $user) : void
    {
        $message = new Message;
        $message->addTo($user->getEmail());
        $message->setSubject("Změna přístupu u projektu {$project->getName()}");
        $message->setBody(
            "Dobrý den,\n\r" .
            "právě Vám byl odebrán přístup u projektu '{$project->getName()}'.\n\r" .
            "Pro správu ostatních projektů se můžete přihlásit na webu {$this->domain}."
        );
        $this->send($message);
    }
}
