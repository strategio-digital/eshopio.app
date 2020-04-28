<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\RemoveModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Database\Manager\SegmentManager;
use App\Database\Repository\SegmentRepository;
use Nette\Application\UI\Form;

class RemoveModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SegmentRepository
     */
    protected SegmentRepository $segmentRepository;

    /**
     * @var SegmentManager
     */
    protected SegmentManager $segmentManager;

    /**
     * @var int|null
     */
    public ?int $id = NULL;

    /**
     * RemoveModal constructor.
     * @param SegmentRepository $segmentRepository
     * @param SegmentManager $segmentManager
     */
    public function __construct(SegmentRepository $segmentRepository, SegmentManager $segmentManager)
    {
        $this->segmentRepository = $segmentRepository;
        $this->segmentManager = $segmentManager;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/remove-modal.latte');
    }

    protected function createComponentForm() : Form
    {
        $form = new Form;
        $form->addHidden('id')
            ->addRule(Form::INTEGER, 'Id segmentu musí být celé číslo.')
            ->setRequired('Id segmentu je povinné.')
            ->setDefaultValue($this->id);

        $form->addSubmit('save');

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $segment = $this->segmentRepository->findOneById($values->id);
            $this->segmentManager->remove($segment);

            $form->reset();
            $this->redrawControl('form');

            $this->toggleModal($this->getName(), 'hide');
            $this->notification(Notification::SUCCESS, 'Segment', "Segment '{$segment->getName()}' byl úspěšně odstraněn.", 4000, FALSE);

            $this->presenter->getComponent('segmentSummary')->redrawControl('summary');
        };

        return $form;
    }

    /**
     * @param int $id
     * @throws \Nette\Application\AbortException
     */
    public function handleOpen(int $id) : void
    {
        $this->id = $id;
        $this->toggleModal($this->getName(), 'show');
        $this->redrawControl('form');
    }
}
