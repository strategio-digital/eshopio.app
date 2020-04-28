<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\Segment\AddModal\AddModal;
use App\Component\Segment\AddModal\IAddModal;
use App\Component\Segment\EditModal\EditModal;
use App\Component\Segment\EditModal\IEditModal;
use App\Component\Segment\RemoveModal\RemoveModal;
use App\Component\Segment\RemoveModal\IRemoveModal;
use App\Component\Segment\Summary\ISummary;
use App\Component\Segment\Summary\Summary;

final class SegmentPresenter extends BaseProjectPresenter
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
        $this->template->seoTitle = 'Segmentace kontaktů';

        $this->breadCrumb[] = new BreadCrumbItem('Kontakty', 'Contact:summary');
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    protected function createComponentSegmentAddModal() : AddModal
    {
        return $this->IAddModal->create($this->activeProject);
    }

    protected function createComponentSegmentEditModal() : EditModal
    {
        return $this->IEditModal->create();
    }

    protected function createComponentSegmentRemoveModal() : RemoveModal
    {
        return $this->IRemoveModal->create();
    }

    protected function createComponentSegmentSummary() : Summary
    {
        return $this->ISummary->create($this->activeProject);
    }
}
