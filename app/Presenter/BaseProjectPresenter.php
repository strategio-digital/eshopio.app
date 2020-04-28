<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\Project;
use App\Database\Repository\ProjectRepository;
use App\Storage\ProjectStorage;

abstract class BaseProjectPresenter extends BasePresenter
{
    /**
     * @var ProjectStorage
     * @inject
     */
    public ProjectStorage $projectStorage;

    /**
     * @var ProjectRepository
     * @inject
     */
    public ProjectRepository $projectRepository;

    /**
     * @var Project|null
     */
    protected ?Project $activeProject;


    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     * @throws \Nette\Application\UI\InvalidLinkException
     */
    public function startup() : void
    {
        parent::startup();

        $activeProject = $this->projectStorage->getActiveProject();

        if (!$this->isLinkCurrent('Project:summary') && !$this->isLinkCurrent('Project:select'))
        {
            if (!$activeProject) {
                $this->notificationFlash(Notification::DANGER, 'Projekt', 'Projekt který se pokoušíte spravovat nejspíše neexistuje, vyberte prosím jiný.', 4000);
                $this->redirect('Project:summary');
                exit;
            }

            $isAllowed = $this->projectRepository->isAllowedForUser($activeProject, $this->activeUser);

            if (!$isAllowed) {
                $this->notificationFlash(Notification::DANGER, 'Projekt', "K projektu '{$activeProject->getName()}' nemáte povolen přístup.", 4000);
                $this->redirect('Project:summary');
                exit;
            }
        }

        // Persistent entity
        if ($activeProject) {
            $activeProject = $this->projectRepository->findOneById($activeProject->getId(), TRUE);
            $this->breadCrumb[0] = new BreadCrumbItem($activeProject->getName(), NULL);
        }

        $this->activeProject = $activeProject;
    }
}
