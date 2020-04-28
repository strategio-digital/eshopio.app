<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\EmailCampaign\Summary;

use App\Component\BaseComponent;
use App\Database\Entity\Project;
use App\Database\Repository\EmailCampaignRepository;

class Summary extends BaseComponent
{
    /**
     * @var EmailCampaignRepository
     */
    protected EmailCampaignRepository $emailCampaignRepository;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * Summary constructor.
     * @param EmailCampaignRepository $emailCampaignRepository
     * @param Project $activeProject
     */
    public function __construct(EmailCampaignRepository $emailCampaignRepository, Project $activeProject)
    {
        $this->emailCampaignRepository = $emailCampaignRepository;
        $this->activeProject = $activeProject;
    }

    public function render() : void
    {
        $emailCampaigns = $this->emailCampaignRepository->findAllByProject($this->activeProject);

        $this->template->render(__DIR__ . '/templates/summary.latte', [
            'emailCampaigns' => $emailCampaigns
        ]);
    }
}
