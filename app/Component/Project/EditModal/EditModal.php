<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Project\EditModal;

use App\Component\Notification\Entity\Notification;
use App\Component\Project\AddModal\AddModal;
use App\Database\Manager\ProjectManager;
use App\Database\Repository\ProjectRepository;
use Nette\Application\UI\Form;

class EditModal extends AddModal
{
    /**
     * @var ProjectRepository
     */
    protected ProjectRepository $projectRepository;

    /**
     * @var int|null
     * @persistent
     */
    public ?int $id = NULL;

    /**
     * @var string|null
     * @persistent
     */
    public ?string $secretKey = NULL;

    /**
     * EditModal constructor.
     * @param ProjectRepository $projectRepository
     * @param ProjectManager $projectManager
     */
    public function __construct(ProjectRepository $projectRepository, ProjectManager $projectManager)
    {
        $this->projectRepository = $projectRepository;
        $this->projectManager = $projectManager;
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = parent::createComponentForm();

        if ($this->id && $this->secretKey)
        {
            $project = $this->projectRepository->findOneByKeyAndId($this->id, $this->secretKey);

            $form->addHidden('secretKey', $project->getSecretKey());
            $form->addHidden('id', (string) $project->getId());
            $form->getComponent('name')->setDefaultValue($project->getName());
        }

        $form->onSuccess[0] = function (Form $form, \stdClass $values) {
            // Update project
            $project = $this->projectRepository->findOneByKeyAndId($this->id, $this->secretKey);
            $this->projectManager->updateName($project, $values->name);

            // Toggle modal & Notification
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Projekt', "Název projektu byl upraven na '{$values->name}'.", 4000, FALSE);

            // Redraw projectSummary
            $this->presenter->getComponent('projectSummary')->redrawControl('summary');
        };

        return  $form;
    }

    /**
     * @param int $id
     * @param string $secretKey
     * @throws \Nette\Application\AbortException
     */
    public function handleOpen(int $id, string $secretKey) : void
    {
        $this->id = $id;
        $this->secretKey = $secretKey;
        $this->toggleModal($this->getName(), 'show', FALSE);
        $this->redrawControl('form');
    }
}
