<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\SelectForm;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\User;
use App\Database\Repository\ProjectRepository;
use App\Storage\ProjectStorage;
use Nette\Application\UI\Form;

class SelectForm extends BaseComponent
{
    use FormDisplayError;

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
    protected User $activeuser;

    /**
     * SelectForm constructor.
     * @param ProjectRepository $projectRepository
     * @param ProjectStorage $projectStorage
     * @param User $activeUser
     */
    public function __construct(ProjectRepository $projectRepository, ProjectStorage $projectStorage, User $activeUser)
    {
        $this->projectRepository = $projectRepository;
        $this->projectStorage = $projectStorage;
        $this->activeuser = $activeUser;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $projects = $this->projectRepository->findAllByUserWithUsers($this->activeuser);

        $this->template->render(__DIR__ . '/templates/select-form.latte', [
            'projects' => $projects
        ]);
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $projects = $this->projectRepository->findAllByUserWithUsers($this->activeuser);
        $projectByIds = [];

        foreach ($projects as $project) {
            $projectByIds[$project->getId()] = $project->getName();
        }

        $form = new Form;
        $form->addSelect('projectId')
            ->setItems($projectByIds)
            ->setRequired('Výběr projektu je povinný.');

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $project = $this->projectRepository->findOneById($values->projectId, TRUE);
            $isAllowed = $this->projectRepository->isAllowedForUser($project, $this->activeuser);

            if ($isAllowed) {
                $this->projectStorage->setActiveProject($project);
                $this->notificationFlash(Notification::SUCCESS, 'Projekt', "Zvolili jste projekt '{$project->getName()}', nyní jej můžete spravovat.");
                $this->presenter->redirect('EmailCampaign:summary');
            }  else {
                $this->notification(Notification::DANGER, 'Projekt', 'Zvolili jste projekt, ke kterému již nemáte přístup.', 4000);
            }
        };

        return $form;
    }
}
