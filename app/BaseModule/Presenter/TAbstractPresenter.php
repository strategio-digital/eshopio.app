<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Presenter;

use App\BaseModule\Component\Notification\Entity\Notification;
use Latte\Engine;
use Nette\Application\AbortException;

/**
 * Trait TBasePresenter
 * @property AbstractPresenter $presenter
 * @property Engine $template
 */
trait TAbstractPresenter
{
    /**
     * @param string $type
     * @param string $title
     * @param string $message
     * @param int $delay
     * @param bool $sendPayload
     * @throws AbortException
     */
    public function notification(string $type, string $title, string $message, int $delay = 0, bool $sendPayload = TRUE) : void
    {
        $this->presenter->notifications[] = new Notification($type, $title, $message, $delay);

        $this->presenter->payload->notifications = $this->presenter->template->renderToString(
            __DIR__ . '/../Component/Notification/templates/notification.latte', [
            'notifications' => $this->presenter->notifications
        ]);

        if ($sendPayload) {
            $this->presenter->sendPayload();
        }
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $message
     * @param int $delay
     */
    public function notificationFlash(string $type, string $title, string $message, int $delay = 0) : void
    {
        $id = $this->presenter->getParameterId('flash');
        $notifications = $this->presenter->getPresenter()->getFlashSession()->$id;
        $notifications[] = new Notification($type, $title, $message, $delay);
        $this->presenter->getTemplate()->flashes = $notifications;
        $this->presenter->getPresenter()->getFlashSession()->$id = $notifications;
    }

    /**
     * @param string $element
     * @param string $toggle
     * @param bool $sendPayload
     * @throws AbortException
     */
    public function toggleModal(string $element, string $toggle = 'hide', bool $sendPayload = FALSE) : void
    {
        $this->presenter->payload->toggleModal = [
            'element' => $element,
            'toggle' => $toggle
        ];

        if ($sendPayload) {
            $this->presenter->sendPayload();
        }
    }
}
