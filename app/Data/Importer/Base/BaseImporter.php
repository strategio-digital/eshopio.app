<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Data\Importer\Base;

use App\Database\Entity\Project;
use App\Database\Entity\Segment;
use App\Database\Manager\ContactManager;
use App\Database\Repository\ContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;
use Ramsey\Uuid\Uuid;

abstract class BaseImporter implements IBaseImporter
{
    /**
     * Temp path to store imported files
     */
    const TEMP_PATH = __DIR__ . '/../../../../assets/dynamic/import/';

    /**
     * @var ContactRepository
     */
    private ContactRepository $contactRepository;

    /**
     * @var ContactManager
     */
    private ContactManager $contactManager;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $importedEntities;

    /**
     * @var int
     */
    private int $exceptionCount = 0;

    /**
     * BaseImporter constructor.
     * @param ContactRepository $contactRepository
     * @param ContactManager $contactManager
     */
    public function __construct(ContactRepository $contactRepository, ContactManager $contactManager)
    {
        $this->contactRepository = $contactRepository;
        $this->contactManager = $contactManager;
        $this->importedEntities = new ArrayCollection;
    }

    /**
     * @param ImportedEntity $importedEntity
     */
    public function addImportedEntity(ImportedEntity $importedEntity) : void
    {
        $this->exceptionCount += count($importedEntity->getExceptions());
        $this->importedEntities->add($importedEntity);
    }

    /**
     * @param ArrayCollection|ImportedEntity[] $importedEntities
     * @return ArrayCollection|ImportedEntity[]
     */
    public function checkDuplicities(ArrayCollection $importedEntities) : ArrayCollection
    {
        $names = $emails = $phones = [];

        foreach ($importedEntities as $key => $importedEntity)
        {
            $names[$importedEntity->getName()][] = $key;

            if ($importedEntity->getEmail()) {
                $emails[$importedEntity->getEmail()][] = $key;
            }

            if ($importedEntity->getPhone()) {
                $phones[$importedEntity->getPhone()][] = $key;
            }
        }

        foreach ($names as $keyArray) {
            if (count($keyArray) > 1) {
                foreach ($keyArray as $key) {
                    $this->exceptionCount++;
                    $importedEntities->get($key)->addException("Duplicitní název.");
                }
            }
        }

        foreach ($emails as $keyArray) {
            if (count($keyArray) > 1) {
                foreach ($keyArray as $key) {
                    $this->exceptionCount++;
                    $importedEntities->get($key)->addException("Duplicitní e-mail.");
                }
            }
        }

        foreach ($phones as $keyArray) {
            if (count($keyArray) > 1) {
                foreach ($keyArray as $key) {
                    $this->exceptionCount++;
                    $importedEntities->get($key)->addException("Duplicitní tel. číslo.");
                }
            }
        }

        return $importedEntities;
    }

    /**
     * @param Project $project
     * @param ArrayCollection|ImportedEntity[] $importedEntities
     * @return ArrayCollection|ImportedEntity[]
     */
    public function checkExistingData(Project $project, ArrayCollection $importedEntities) : ArrayCollection
    {
        foreach ($importedEntities as $key => $importedEntity)
        {
            if ($importedEntity->getEmail()) {
                if ($this->contactRepository->findOneByProjectAndEmail($project, $importedEntity->getEmail())) {
                    $this->exceptionCount++;
                    $importedEntity->addException("E-mail v projektu již existuje.");
                }
            }

            if ($importedEntity->getPhone()) {
                if ($this->contactRepository->findOneByProjectAndPhone($project, $importedEntity->getPhone())) {
                    $this->exceptionCount++;
                    $importedEntity->addException("Tel. číslo v projektu již existuje.");
                }
            }
        }

        return $importedEntities;
    }

    /**
     * @param Project $project
     * @param Segment $segment
     * @param ArrayCollection|ImportedEntity[] $importedEntities
     * @throws \Exception
     */
    public function save(Project $project, Segment $segment, ArrayCollection $importedEntities) : void
    {
        $this->contactManager->beginTransaction();

        try {
            foreach ($importedEntities as $importedEntity) {
                if ($importedEntity->getPhone() && $importedEntity->getEmail()) {
                    $this->contactManager->create($project, $segment, $importedEntity->getEmail(), $importedEntity->getPhone(), $importedEntity->getName());
                } else if ($importedEntity->getEmail()) {
                    $this->contactManager->createByEmail($project, $segment, $importedEntity->getEmail(), $importedEntity->getName());
                } else if ($importedEntity->getPhone()) {
                    $this->contactManager->createByPhone($project, $segment, $importedEntity->getPhone(), $importedEntity->getName());
                }
            }

            $this->contactManager->commit();

        } catch (\Exception $exception) {
            $this->contactManager->rollback();
            throw $exception;
        }
    }

    /**
     * @return int
     */
    public function getExceptionCount() : int
    {
        return $this->exceptionCount;
    }

    /**
     * @return ArrayCollection|ImportedEntity[]
     */
    protected function getImportedEntities() : ArrayCollection
    {
        return $this->importedEntities;
    }

    /**
     * @param FileUpload $fileUpload
     * @return \SplFileInfo
     * @throws \Exception
     */
    protected function saveTempFile(FileUpload $fileUpload) : \SplFileInfo
    {
        $name = Uuid::uuid4()->toString() . $fileUpload->getName();

        $fileUpload->move(self::TEMP_PATH . $name);

        return new \SplFileInfo(self::TEMP_PATH . $name);
    }

    /**
     * @param \SplFileInfo $splFileInfo
     */
    protected function removeTempFile(\SplFileInfo $splFileInfo) : void
    {
        FileSystem::delete($splFileInfo->getRealPath());
    }
}