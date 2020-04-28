<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\BaseModule\Component\Notification\Entity;

class Notification
{
    const DANGER = 'danger';
    const WARNING = 'warning';
    const SUCCESS = 'success';
    const INFO = 'info';

    /**
     * @var string
     */
    protected string $type;

    /**
     * @var string
     */
    protected string $title;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @var int
     */
    protected int $delay = 0;

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getDelay() : int
    {
        return $this->delay;
    }

    /**
     * Notification constructor.
     * @param string $type
     * @param string $title
     * @param string $message
     * @param int $delay
     */
    public function __construct(string $type, string $title, string $message, int $delay = 0)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->delay = $delay;
    }
}
