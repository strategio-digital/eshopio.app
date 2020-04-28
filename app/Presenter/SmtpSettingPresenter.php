<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Presenter;

use App\Component\BreadCrumb\Entity\BreadCrumbItem;
use App\Component\SmtpSetting\AddModal\AddModal;
use App\Component\SmtpSetting\AddModal\IAddModal;
use App\Component\SmtpSetting\EditModal\EditModal;
use App\Component\SmtpSetting\EditModal\IEditModal;
use App\Component\SmtpSetting\RemoveModal\IRemoveModal;
use App\Component\SmtpSetting\RemoveModal\RemoveModal;
use App\Component\SmtpSetting\Summary\ISummary;
use App\Component\SmtpSetting\Summary\Summary;

final class SmtpSettingPresenter extends BaseProjectPresenter
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
        $this->template->seoTitle = 'Nastavení SMTP';

        $this->breadCrumb[] = new BreadCrumbItem('E-mailing', 'EmailCampaign:summary');
        $this->breadCrumb[] = new BreadCrumbItem($this->template->seoTitle, NULL);
    }

    protected function createComponentSmtpSettingAddModal() : AddModal
    {
        return $this->IAddModal->create($this->activeProject);
    }

    protected function createComponentSmtpSettingRemoveModal() : RemoveModal
    {
        return $this->IRemoveModal->create();
    }

    protected function createComponentSmtpSettingSummary() : Summary
    {
        return $this->ISummary->create($this->activeProject, $this->activeUser);
    }

    protected function createComponentSmtpSettingEditModal() : EditModal
    {
        return $this->IEditModal->create();
    }
}
