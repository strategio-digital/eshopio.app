<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Component;

use App\BaseModule\Component\Notification\Entity\Notification;
use App\BaseModule\Presenter\BasePresenter;
use Nette\Application\UI\Form;

/**
 * Trait FormValidation
 * @package App\Component
 * @property BasePresenter $presenter
 */
trait FormDisplayError
{
    /**
     * @param Form $form
     * @throws \Nette\Application\AbortException
     */
    public function showNotifications(Form $form) : void
    {
        $errors = $form->getErrors();
        $count = count($errors);

        for ($i = 0; $i < $count; $i++) {
            $isLast = $i === $count-1;
            $this->notification(Notification::DANGER, 'Neplatné hodnoty', $errors[$i], 4000, $isLast);
        }
    }
}
