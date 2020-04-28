<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\Contact\AddModal\AddModal;
use App\Component\Contact\AddModal\IAddModal;
use App\Component\Contact\EditModal\EditModal;
use App\Component\Contact\EditModal\IEditModal;
use App\Component\Contact\ImportModal\IImportModal;
use App\Component\Contact\ImportModal\ImportModal;
use App\Component\Contact\RemoveModal\IRemoveModal;
use App\Component\Contact\RemoveModal\RemoveModal;
use App\Component\Contact\Summary\ISummary;
use App\Component\Contact\Summary\Summary;

final class ContactPresenter extends BaseProjectPresenter
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
     * @var IImportModal
     * @inject
     */
    public IImportModal $IImportModal;

    /**
     * @var IRemoveModal
     * @inject
     */
    public IRemoveModal $IRemoveModal;

    /**
     * @var ISummary
     * @inject
     */
    public ISummary $ISummary;

    public function actionSummary() : void
    {
        $this->template->seoTitle = 'Přehled kontaktů';

        $this->breadCrumb[] = new BreadCrumbItem('Kontakty', NULL);
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    protected function createComponentContactAddModal() : AddModal
    {
        return $this->IAddModal->create($this->activeProject);
    }

    protected function createComponentContactEditModal() : EditModal
    {
        return $this->IEditModal->create($this->activeProject);
    }

    protected function createComponentContactImportModal() : ImportModal
    {
        return $this->IImportModal->create($this->activeProject);
    }

    protected function createComponentContactRemoveModal() : RemoveModal
    {
        return $this->IRemoveModal->create();
    }

    protected function createComponentContactSummary() : Summary
    {
        return $this->ISummary->create($this->activeProject);
    }
}
