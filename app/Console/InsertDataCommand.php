<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Console;

use App\Database\Manager\AllowedDayManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InsertDataCommand extends Command
{
    /**
     * @var AllowedDayManager
     */
    protected AllowedDayManager $allowedDayManager;

    /**
     * InsertDataCommand constructor.
     * @param AllowedDayManager $allowedDayManager
     */
    public function __construct(AllowedDayManager $allowedDayManager)
    {
        $this->allowedDayManager = $allowedDayManager;
        parent::__construct();
    }

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('wakers:insert-data')
            ->setDescription('Create core data in database');
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->allowedDayManager->createDays();

            $output->writeln("<info>Core DB data successfully initialized</info>");
        } catch (\Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
        }

        return 0;
    }
}
