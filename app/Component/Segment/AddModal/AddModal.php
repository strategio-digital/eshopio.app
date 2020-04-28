<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\AddModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Entity\Project;
use App\Database\Manager\SegmentManager;
use Nette\Application\UI\Form;

class AddModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SegmentManager
     */
    protected SegmentManager $segmentManager;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * AddModal constructor.
     * @param SegmentManager $segmentManager
     * @param Project $activeProject
     */
    public function __construct(SegmentManager $segmentManager, Project $activeProject)
    {
        $this->segmentManager = $segmentManager;
        $this->activeProject = $activeProject;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/add-modal.latte');
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = new Form;
        $form->addText('name')->setRequired('Název segmentu je povinný.');
        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $segment = $this->segmentManager->create($this->activeProject, $values->name);

            // Toggle modal & add notification
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Segment', "Segment s názvem '{$segment->getName()}' byl úspěšně vytvořen.", 4000, FALSE);

            // Reset form
            $form->reset();
            $this->redrawControl('form');

            // Redraw summary
            $this->presenter->getComponent('segmentSummary')->redrawControl('summary');
        };

        return $form;
    }
}
