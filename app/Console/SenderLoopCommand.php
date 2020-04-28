<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Console;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\SentEmail;
use App\Database\Entity\SmtpSetting;
use App\Database\Manager\ContactManager;
use App\Database\Manager\EmailCampaignManager;
use App\Database\Manager\RateManager;
use App\Database\Manager\SentEmailManager;
use App\Database\Manager\SmtpSettingManager;
use App\Database\Repository\EmailCampaignRepository;
use App\Database\Repository\SentEmailRepository;
use App\Mailer\Campaign\CampaignMailer;
use Spatie\Async\Pool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;
use Tracy\Debugger;

class SenderLoopCommand extends Command
{
    /**
     * @var EmailCampaignRepository
     */
    protected EmailCampaignRepository $emailCampaignRepository;

    /**
     * @var SentEmailRepository
     */
    protected SentEmailRepository $sentEmailRepository;

    /**
     * @var EmailCampaignManager
     */
    protected EmailCampaignManager $emailCampaignManager;

    /**
     * @var SentEmailManager
     */
    protected SentEmailManager $sentEmailManager;

    /**
     * @var SmtpSettingManager
     */
    protected SmtpSettingManager $smtpSettingManager;

    /**
     * @var RateManager
     */
    protected RateManager $rateManager;

    /**
     * @var ContactManager
     */
    protected ContactManager $contactManager;

    /**
     * @var CampaignMailer
     */
    protected CampaignMailer $campaignMailer;


    /**
     * SenderLoopCommand constructor.
     * @param EmailCampaignRepository $emailCampaignRepository
     * @param SentEmailRepository $sentEmailRepository
     * @param EmailCampaignManager $emailCampaignManager
     * @param SentEmailManager $sentEmailManager
     * @param SmtpSettingManager $smtpSettingManager
     * @param RateManager $rateManager
     * @param ContactManager $contactManager
     * @param CampaignMailer $campaignMailer
     */
    public function __construct(
        EmailCampaignRepository $emailCampaignRepository,
        SentEmailRepository $sentEmailRepository,
        EmailCampaignManager $emailCampaignManager,
        SentEmailManager $sentEmailManager,
        SmtpSettingManager $smtpSettingManager,
        RateManager $rateManager,
        ContactManager $contactManager,
        CampaignMailer $campaignMailer
    ) {
        parent::__construct();

        $this->sentEmailRepository = $sentEmailRepository;
        $this->emailCampaignRepository = $emailCampaignRepository;
        $this->emailCampaignManager = $emailCampaignManager;
        $this->sentEmailManager = $sentEmailManager;
        $this->smtpSettingManager = $smtpSettingManager;
        $this->rateManager = $rateManager;
        $this->contactManager = $contactManager;
        $this->campaignMailer = $campaignMailer;
    }


    /**
     * Configure
     */
    protected function configure()
    {
        $this
            ->setName('wakers:start-sender-loop')
            ->setDescription('Run infinity loop for sending all e-mail campaigns.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $zeroExecutionTime = ini_get('max_execution_time') === '0';

        $output->writeln('Zero execution time: ' . ($zeroExecutionTime  ? '<info>OK</info>' : '<error>FAILED</error>'));
        $output->writeln('Supported async PHP: ' . (Pool::isSupported() ? '<info>OK</info>' : '<error>FAILED</error>'));

        if (!$zeroExecutionTime) {
            throw new \Exception('Cannot start infinity loop with max_execution_time time.');
        }

        if (!Pool::isSupported()) {
            throw new \Exception('Async PHP is not supported. Please install PCNTL extension.');
        }

        $cloner = new VarCloner();
        $dumper = new ServerDumper('tcp://127.0.0.1:9912',  new CliDumper(), [
            'cli' => new CliContextProvider(),
            'source' => new SourceContextProvider(),
        ]);

        VarDumper::setHandler(function ($var) use ($cloner, $dumper) {
            $dumper->dump($cloner->cloneVar($var));
        });

        while (true) {
            $this->loopTick($input, $output);
            usleep(1000 * 1000);
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function loopTick(InputInterface $input, OutputInterface $output) : void
    {
        $startedAt = microtime(true);

        $this->smtpSettingManager->resetDayLimits();
        $this->smtpSettingManager->resetMinuteLimits();

        $emailCampaigns = $this->emailCampaignRepository->findAllActive();

        $output->writeln('<info>Campaigns in queue:</info> ' . $emailCampaigns->count());
        $output->writeln('<info>Loop started at:</info> ' . (new \DateTime())->format('H:i:s'));

        $pool = Pool::create()->concurrency(15)->timeout(15)->sleepTime(50000);

        foreach ($emailCampaigns as $emailCampaign) {
            // Pokud by mělo běžet více instancí senderLoop, je omezit podmínku na SentEmail::STATUS_WAITING
            $sentEmail = $this->sentEmailRepository->findLastInQueue($emailCampaign);

            // Prevence proti jednomu SMTP přiřazenému u více kampaní (pokud bylo dosaženo limitů tak se odesálání přeskočí)
            if ($sentEmail) {
                $smtpSettings = $emailCampaign->getSmtpSetting();
                $this->smtpSettingManager->increaseLimits($smtpSettings);

                // Pokud by mělo běžet více instancí senderLoop, je potřeba e-maily značkovat, že již jsou ve frontě ke zpracování
                // Při chybě změnit zpět na STATUS_WAITING, při dokončení STATUS_PROCEEDED.
                //$this->sentEmailManager->updateQueueStatus(SentEmail::STATUS_IN_QUEUE);

                // Převod objektů na flat-array, které je přípustné pro Pool->add(function() use ($array, ...) {})
                $campaignArray = $this->toArrayCampaign($emailCampaign, $sentEmail);
                $smtpArray = $this->toArraySmtp($smtpSettings);

                $pool
                    ->add(function () use ($campaignArray, $smtpArray) {
                        $message = new \Nette\Mail\Message;
                        $message->setFrom($campaignArray['senderEmail'], $campaignArray['senderName']);
                        $message->addTo($campaignArray['to']);
                        $message->setSubject($campaignArray['subject']);
                        $message->setHtmlBody($campaignArray['message']);

                        $smtp = new \Nette\Mail\SmtpMailer($smtpArray);
                        $smtp->send($message);
                    })
                    ->then(function () use ($emailCampaign, $sentEmail) {

                        // Mark as sent
                        try {
                            $this->sentEmailManager->updateSentTime($sentEmail);
                        } catch (\Exception $exception) {
                            Debugger::log($exception->getMessage());
                        }

                        // Recalculate Click-Rate & OpenRate
                        $realSentEmailCount = $this->sentEmailRepository->countByEmailCampaignSentTimeNotNull($emailCampaign);

                        if ($realSentEmailCount !== 0) {
                            $this->rateManager->updateOpenRate($emailCampaign, $realSentEmailCount);
                            $this->rateManager->updateClickRate($emailCampaign, $realSentEmailCount);
                        }

                        // Is campaign is finished, then set status
                        $sentEmailCount = $this->sentEmailRepository->countByEmailCampaign($emailCampaign);

                        if ($realSentEmailCount === $sentEmailCount) {
                            $this->emailCampaignManager->updateStatus($emailCampaign, EmailCampaign::STATUS_FINISHED);
                        }

                        // Update stats
                        try {
                            $this->contactManager->updateStats($sentEmail->getContact());
                        } catch (\Exception $exception) {
                            Debugger::log($exception->getMessage());
                        }

                        echo 'Updated SentEmail.id: ' . $sentEmail->getId() . PHP_EOL;
                    })
                    ->catch(function (\Exception $exception) use ($smtpSettings) {
                        try {
                            $this->smtpSettingManager->decreaseLimits($smtpSettings);
                        } catch (\Exception $exception) {
                            Debugger::log($exception->getMessage());
                        }

                        Debugger::log($exception->getMessage());
                    })
                    ->timeout(function () use ($smtpSettings) {
                        $this->smtpSettingManager->decreaseLimits($smtpSettings);
                        Debugger::log('Timeout for SMTP: ' . $smtpSettings->getHost());
                    })
                ;
            }
        }

        $pool->wait();
        $this->emailCampaignRepository->clear();

        $output->writeln('<info>Loop finished after:</info> ' . (microtime(true) - $startedAt) . 's');
    }

    /**
     * @param EmailCampaign $emailCampaign
     * @param SentEmail $sentEmail
     * @return array
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    protected function toArrayCampaign(EmailCampaign $emailCampaign, SentEmail $sentEmail) : array
    {
        $message = $this->campaignMailer->createHtmlMessage($emailCampaign, $sentEmail);

        return [
            'subject' => $emailCampaign->getSubject(),
            'message' => $message,
            'to' => $sentEmail->getContact()->getEmail(),
            'senderName' => $emailCampaign->getSmtpSetting()->getSenderName(),
            'senderEmail' => $emailCampaign->getSmtpSetting()->getSenderEmail(),
        ];
    }

    /**
     * @param SmtpSetting $smtpSetting
     * @return array
     */
    protected function toArraySmtp(SmtpSetting $smtpSetting) : array
    {
        $smtp = [
            'username' => $smtpSetting->getUsername(),
            'host' =>$smtpSetting->getHost(),
            'password' => $smtpSetting->getPassword(),
            'port' => $smtpSetting->getPort(),
            'persistent' => TRUE
        ];

        if ($smtpSetting->getSecure()) {
            $smtp['secure'] = $smtpSetting->getSecure();
        }

        return $smtp;
    }
}
