<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\Project\AddModal\AddModal;;
use App\Component\Project\AddModal\IAddModal;
use App\Component\Project\CollaboratorModal\CollaboratorModal;
use App\Component\Project\CollaboratorModal\ICollaboratorModal;
use App\Component\Project\EditModal\EditModal;
use App\Component\Project\EditModal\IEditModal;
use App\Component\Project\SelectForm\ISelectForm;
use App\Component\Project\SelectForm\SelectForm;
use App\Component\Project\Summary\ISummary;
use App\Component\Project\Summary\Summary;

final class ProjectPresenter extends BaseProjectPresenter
{
    /**
     * @var IAddModal
     * @inject
     */
    public IAddModal $IAddModal;

    /**
     * @var IEditModal
     * @inject
     */
    public IEditModal $IEditModal;

    /**
     * @var ICollaboratorModal
     * @inject
     */
    public ICollaboratorModal $ICollaboratorModal;

    /**
     * @var ISummary
     * @inject
     */
    public ISummary $ISummary;

    /**
     * @var ISelectForm
     * @inject
     */
    public ISelectForm $ISelectForm;

    public function actionSummary() : void
    {
        $this->template->seoTitle = 'Přehled projektů';

        $this->breadCrumb[] = new BreadCrumbItem('Projekty', NULL);
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    public function actionSelect() : void
    {
        $this->template->seoTitle = 'Výběr projektu';
    }

    public function createComponentProjectAddModal() : AddModal
    {
        return $this->IAddModal->create($this->activeUser);
    }

    public function createComponentProjectEditModal(): EditModal
    {
        return $this->IEditModal->create();
    }

    public function createComponentProjectCollaboratorModal(): CollaboratorModal
    {
        return $this->ICollaboratorModal->create($this->activeUser);
    }

    protected function createComponentProjectSummary() : Summary
    {
        return $this->ISummary->create($this->activeUser, $this->activeProject);
    }

    protected function createComponentProjectSelectForm() : SelectForm
    {
        return $this->ISelectForm->create($this->activeUser);
    }
}
