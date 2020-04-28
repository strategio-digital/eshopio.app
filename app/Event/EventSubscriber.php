<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents() : array
    {
        return [
            //\App\Component\Project\AddModal\Event\SuccessForm::class => 'myEvent'
        ];
    }

    public function myEvent(string $event) : void
    {
        bdump($event);
    }
}
