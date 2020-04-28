<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Console;

use App\Database\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    /**
     * @var UserManager
     */
    protected UserManager $userManager;

    /**
     * CreateUser constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('wakers:create-user')
            ->setDescription('Create a new user and save it to the database.')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        try {
            $user = $this->userManager->create($email, $password);
            $output->writeln("<info>User {$user->getEmail()} has been created.</info>");
        } catch (\Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
        }

        return 0;
    }
}
