<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\Summary;

use App\Component\BaseComponent;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\Project;
use App\Database\Entity\User;
use App\Database\Repository\ProjectRepository;
use App\Storage\ProjectStorage;

class Summary extends BaseComponent
{
    /**
     * @var ProjectRepository
     */
    protected ProjectRepository $projectRepository;

    /**
     * @var ProjectStorage
     */
    protected ProjectStorage $projectStorage;

    /**
     * @var User
     */
    protected User $activeUser;

    /**
     * @var Project|null
     */
    protected ?Project $activeProject;

    /**
     * Summary constructor.
     * @param ProjectRepository $projectRepository
     * @param User $activeUser
     * @param ProjectStorage $projectStorage
     * @param Project|null $activeProject
     */
    public function __construct(
        ProjectRepository $projectRepository,
        User $activeUser,
        ProjectStorage $projectStorage,
        ?Project $activeProject
    ) {
        $this->projectRepository = $projectRepository;
        $this->activeUser = $activeUser;
        $this->projectStorage = $projectStorage;
        $this->activeProject = $activeProject;
    }

    public function render() : void
    {
        $projects = $this->projectRepository->findAllByUserWithUsers($this->activeUser);

        $this->template->render(__DIR__ . '/templates/summary.latte', [
            'activeProject' => $this->activeProject,
            'projects' => $projects
        ]);
    }

    /**
     * @param int $id
     * @param string $secretKey
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Nette\Application\AbortException
     */
    public function handleSelectProject(int $id, string $secretKey) : void
    {
        $project = $this->projectRepository->findOneByKeyAndId($id, $secretKey);
        $isAllowed = $this->projectRepository->isAllowedForUser($project, $this->activeUser);

        if ($isAllowed) {
            $this->projectStorage->setActiveProject($project);
            $this->notificationFlash(Notification::SUCCESS, 'Projekt', "Zvolili jste projekt '{$project->getName()}', nyní jej můžete spravovat.");
            $this->presenter->redirect('EmailCampaign:summary');

        } else {
            $this->notification(Notification::DANGER, 'Projekt', "K projektu '{$project->getName()}' nemáte povolen přístup.", 4000, FALSE);
            $this->redrawControl('summary');
        }
    }
}
