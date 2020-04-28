<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Database\Manager\RateManager;
use App\Database\Manager\SentEmailManager;
use App\Database\Repository\SentEmailRepository;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Presenter;

class AnalyticsPresenter extends Presenter
{
    /**
     * @var SentEmailRepository
     * @inject
     */
    public SentEmailRepository $sentEmailRepository;

    /**
     * @var SentEmailManager
     * @inject
     */
    public SentEmailManager $sentEmailManager;

    /**
     * @var RateManager
     * @inject
     */
    public RateManager $rateManager;

    /**
     * @param int $emailCampaignId
     * @param int $sentEmailId
     * @param string $emailCampaignSecretKey
     * @param string $sentEmailSecretKey
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\BadRequestException
     * @throws \Exception
     */
    public function actionOpenRate(int $emailCampaignId, int $sentEmailId, string $emailCampaignSecretKey, string $sentEmailSecretKey) : void
    {
        $sentEmail = $this->sentEmailRepository->findOne($emailCampaignId, $sentEmailId, $emailCampaignSecretKey, $sentEmailSecretKey);

        if ($sentEmail) {
            $httpRequest = $this->getHttpRequest();
            $emailCampaign = $sentEmail->getEmailCampaign();

            // OpenRate++
            $this->sentEmailManager->increaseOpenRate($sentEmail, $httpRequest);

            // Update Open-Rate
            $sentEmailCount = $this->sentEmailRepository->countByEmailCampaignSentTimeNotNull($emailCampaign);
            $this->rateManager->updateOpenRate($emailCampaign, $sentEmailCount);
        }

        $response = new FileResponse(__DIR__ . '/../../www/temp/static/img/signature.png', NULL, 'image/png', FALSE);
        $this->sendResponse($response);
    }

    /**
     * @param int $emailCampaignId
     * @param int $sentEmailId
     * @param string $emailCampaignSecretKey
     * @param string $sentEmailSecretKey
     * @param string $targetUrl
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     * @throws \Exception
     */
    public function actionClickRate(int $emailCampaignId, int $sentEmailId, string $emailCampaignSecretKey, string $sentEmailSecretKey, string $targetUrl) : void
    {
        $sentEmail = $this->sentEmailRepository->findOne($emailCampaignId, $sentEmailId, $emailCampaignSecretKey, $sentEmailSecretKey);

        if ($sentEmail) {
            $httpRequest = $this->getHttpRequest();
            $emailCampaign = $sentEmail->getEmailCampaign();

            // ClickRate++
            $this->sentEmailManager->increaseClickRate($sentEmail, $httpRequest);

            // Update Click-Rate & Open-Rate
            $sentEmailCount = $this->sentEmailRepository->countByEmailCampaignSentTimeNotNull($emailCampaign);
            $this->rateManager->updateClickRate($emailCampaign, $sentEmailCount);
            $this->rateManager->updateOpenRate($emailCampaign, $sentEmailCount);
        }

        $this->redirectUrl($targetUrl, 302);
    }
}