<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Console\SenderLoopCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TestPresenter extends BasePresenter
{
    /**
     * @var SenderLoopCommand
     * @inject
     */
    public SenderLoopCommand $senderLoopCommand;

    /**
     * @throws \Exception
     */
    public function renderLoop() : void
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput;

        $this->senderLoopCommand->loopTick($input, $output);
    }
}