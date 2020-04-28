<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\ImportModal;

use App\Component\BaseComponent;
use App\Component\FormDisplayError;
use App\Component\Notification\Entity\Notification;
use App\Data\Importer\Base\ImportedEntity;
use App\Data\Importer\ImportResolver;
use App\Database\Entity\Project;
use App\Database\Repository\SegmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Application\UI\Form;

class ImportModal extends BaseComponent
{
    use FormDisplayError;

    /**
     * @var SegmentRepository
     */
    protected SegmentRepository $segmentRepository;

    /**
     * @var ImportResolver
     */
    protected ImportResolver $importResolver;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * @var ArrayCollection|ImportedEntity[]
     */
    protected ArrayCollection $importedEntities;

    /**
     * ImportModal constructor.
     * @param SegmentRepository $segmentRepository
     * @param ImportResolver $importResolver
     * @param Project $activeProject
     */
    public function __construct(
        SegmentRepository $segmentRepository,
        ImportResolver $importResolver,
        Project $activeProject
    ) {
        $this->segmentRepository = $segmentRepository;
        $this->importResolver = $importResolver;
        $this->activeProject = $activeProject;
        $this->importedEntities = new ArrayCollection;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $this->template->render(__DIR__ . '/templates/import-modal.latte', [
            'importedEntities' => $this->importedEntities
        ]);
    }

    /**
     * @return Form
     * @throws \Exception
     */
    public function createComponentForm() : Form
    {
        $segments = $this->segmentRepository->findAllByProject($this->activeProject);

        $segmentsByIds = [
            NULL => '---'
        ];

        foreach ($segments as $segment) {
            $segmentsByIds[$segment->getId()] = $segment->getName();
        }

        $form = new Form;

        $form->addSelect('segmentId')
            ->setItems($segmentsByIds)
            ->setRequired('Zařazení do segmentu je povinné.');

        $form->addUpload('file')
            ->setRequired('.xlsx / .json soubor je poviný.')
            ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 1MB', $this->importResolver->getMaxFileSize())
            ->addRule(Form::MIME_TYPE, 'Podporované MIME type jsou pouze .json a .xlsx.', $this->importResolver->getAllowedMimeTypes());
            //->addRule(Form::PATTERN, 'Soubor musí mít koncovku .xlsx nebo .json', '.*\.xlsx');

        $form->onValidate[] = function (Form $form, \stdClass $values) {
            $importer = $this->importResolver->getImporter($values->file);
            if (!$importer->load($values->file)) {
                $form->getComponent('file')->addError("Soubor {$values->file->getName()} je ve formátu, který se nedaří přečíst.");
            }
        };

        $form->onError[] = function (Form $form) {
            $this->showNotifications($form);
        };

        $form->onSuccess[] = function (Form $form, \stdClass $values) {
            $importer = $this->importResolver->getImporter($values->file);

            $importedEntities = $importer->extractAndValidate();
            $importedEntities = $importer->checkDuplicities($importedEntities);
            $importedEntities = $importer->checkExistingData($this->activeProject, $importedEntities);

            if ($importer->getExceptionCount() === 0) {
                $segment = $this->segmentRepository->findOneById($values->segmentId);
                $importer->save($this->activeProject, $segment, $importedEntities);

                // Hide & notification
                $this->toggleModal($this->getName(), 'hide');
                $this->notification(Notification::SUCCESS, 'Import', "Úspěšně jste importovali {$importedEntities->count()} kontaktů.", 0, FALSE);

                // Reset importedEntities
                $importedEntities = new ArrayCollection;

                // Reset form
                $form->reset();
                $this->redrawControl('form');

                // Redraw summary
                $this->presenter->getComponent('contactSummary')->redrawControl('summary');

            } else {
                $this->notification(Notification::DANGER, 'Import', "Vyřešte prosím všechny ({$importer->getExceptionCount()}) chyby v importovaném souboru a následně soubor nahrajte znovu.", 0, FALSE);
            }

            $this->importedEntities = $importedEntities;
            $this->redrawControl('contacts');
        };

        return $form;
    }
}
