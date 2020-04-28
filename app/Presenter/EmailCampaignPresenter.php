<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\EmailCampaign\AddForm\AddForm;
use App\Component\EmailCampaign\AddForm\IAddForm;
use App\Component\EmailCampaign\EditForm\EditForm;
use App\Component\EmailCampaign\EditForm\IEditForm;
use App\Component\EmailCampaign\RemoveModal\IRemoveModal;
use App\Component\EmailCampaign\RemoveModal\RemoveModal;
use App\Component\EmailCampaign\SentEmailSummary\SentEmailSummary;
use App\Component\EmailCampaign\SentEmailSummary\ISentEmailSummary;
use App\Component\EmailCampaign\Stats\Stats;
use App\Component\EmailCampaign\Stats\IStats;
use App\Component\EmailCampaign\Summary\ISummary;
use App\Component\EmailCampaign\Summary\Summary;
use App\Component\EmailCampaign\TestModal\ITestModal;
use App\Component\EmailCampaign\TestModal\TestModal;
use App\Database\Entity\EmailCampaign;
use App\Database\Repository\EmailCampaignRepository;
use Nette\Application\BadRequestException;

final class EmailCampaignPresenter extends BaseProjectPresenter
{
    /**
     * @var ISummary
     * @inject
     */
    public ISummary $ISummary;

    /**
     * @var IAddForm
     * @inject
     */
    public IAddForm $IAddForm;

    /**
     * @var IEditForm
     * @inject
     */
    public IEditForm $IEditForm;

    /**
     * @var ITestModal
     * @inject
     */
    public ITestModal $ITestModal;

    /**
     * @var IRemoveModal
     * @inject
     */
    public IRemoveModal $IRemoveModal;

    /**
     * @var IStats
     * @inject
     */
    public IStats $ICampaignStats;

    /**
     * @var ISentEmailSummary
     * @inject
     */
    public ISentEmailSummary $ISentEmailSummary;

    /**
     * @var EmailCampaignRepository
     * @inject
     */
    public EmailCampaignRepository $emailCampaignRepository;

    /**
     * @var EmailCampaign
     */
    protected EmailCampaign $activeEmailCampaign;

    /**
     * Summary
     */
    public function actionSummary() : void
    {
        $this->template->seoTitle = 'Přehled kampaní';

        $this->breadCrumb[] = new BreadCrumbItem('E-mailing', NULL);
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    /**
     * Add
     */
    public function actionAdd() : void
    {
        $this->template->seoTitle = 'Vytvoření kampaně';

        $this->breadCrumb[] = new BreadCrumbItem('E-mailing', 'EmailCampaign:summary');
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    /**
     * Detail
     * @param string $secretKey
     * @param int $id
     * @throws BadRequestException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function actionDetail(string $secretKey, int $id) : void
    {
        $emailCampaign = $this->emailCampaignRepository->findOneBySecretKeyAndId($secretKey, $id);

        if (!$emailCampaign) {
            throw new BadRequestException("Campaing with id: '{$id}' and secretKey: '{$secretKey}' not found.");
        }

        $this->activeEmailCampaign = $emailCampaign;

        $this->template->seoTitle = 'Detail kampaně';
        $this->breadCrumb[] = new BreadCrumbItem('E-mailing', 'EmailCampaign:summary');
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
        $this->breadCrumb[] = new BreadCrumbItem($emailCampaign->getName(), NULL);
    }

    protected function createComponentEmailCampaignSummary() : Summary
    {
        return $this->ISummary->create($this->activeProject);
    }

    protected function createComponentEmailCampaignAddForm() : AddForm
    {
        return $this->IAddForm->create($this->activeProject);
    }

    protected function createComponentEmailCampaignEditForm() : EditForm
    {
        return $this->IEditForm->create($this->activeProject, $this->activeEmailCampaign);
    }

    protected function createComponentEmailCampaignTestModal() : TestModal
    {
        return $this->ITestModal->create($this->activeUser, $this->activeEmailCampaign);
    }

    protected function createComponentEmailCampaignRemoveModal() : RemoveModal
    {
        return $this->IRemoveModal->create($this->activeEmailCampaign);
    }

    protected function createComponentEmailCampaignStats() : Stats
    {
        return $this->ICampaignStats->create($this->activeEmailCampaign);
    }

    protected function createComponentEmailCampaignSentEmailSummary() : SentEmailSummary
    {
        return $this->ISentEmailSummary->create($this->activeEmailCampaign);
    }
}
