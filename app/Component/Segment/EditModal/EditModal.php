<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\EditModal;

use App\Component\Notification\Entity\Notification;
use App\Component\Segment\AddModal\AddModal;
use App\Database\Manager\SegmentManager;
use App\Database\Repository\SegmentRepository;
use Nette\Application\UI\Form;

class EditModal extends AddModal
{
    /**
     * @var SegmentRepository
     */
    protected SegmentRepository $segmentRepository;

    /**
     * @var int|null
     * @persistent
     */
    public ?int $id = NULL;

    /**
     * EditModal constructor.
     * @param SegmentRepository $segmentRepository
     * @param SegmentManager $segmentManager
     */
    public function __construct(SegmentRepository $segmentRepository, SegmentManager $segmentManager)
    {
        $this->segmentRepository = $segmentRepository;
        $this->segmentManager = $segmentManager;
    }

    /**
     * @return Form
     */
    protected function createComponentForm() : Form
    {
        $form = parent::createComponentForm();

        if ($this->id)
        {
            $segment = $this->segmentRepository->findOneById($this->id);

            $form->addHidden('id', (string) $segment->getId());
            $form->getComponent('name')->setDefaultValue($segment->getName());
        }

        $form->onSuccess[0] = function (Form $form, \stdClass $values) {
            // Update segment
            $segment = $this->segmentRepository->findOneById($this->id);

            $this->segmentManager->updateName($segment, $values->name);

            // Toggle modal & Notification
            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Segment', "Název segmentu byl upraven na '{$values->name}'.", 4000, FALSE);

            // Redraw segmentSummary
            $this->presenter->getComponent('segmentSummary')->redrawControl('summary');
        };

        return  $form;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleOpen(int $id) : void
    {
        $this->id = $id;
        $this->toggleModal($this->getName(), 'show', FALSE);
        $this->redrawControl('form');
    }
}
