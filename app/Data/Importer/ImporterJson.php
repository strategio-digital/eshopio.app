<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Data\Importer;

use App\Data\Importer\Base\BaseImporter;
use App\Data\Importer\Base\ImportedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Http\FileUpload;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class ImporterJson extends BaseImporter
{
    /**
     * @var array
     */
    protected array $json;

    /**
     * @param FileUpload $fileUpload
     * @return bool
     */
    public function load(FileUpload $fileUpload) : bool
    {
        try {
            $this->json = Json::decode($fileUpload->getContents(), Json::PRETTY);
        } catch (JsonException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @return ArrayCollection|ImportedEntity[]
     */
    public function extractAndValidate() : ArrayCollection
    {
        foreach ($this->json as $stdContact)
        {
            $name = isset($stdContact->name) ? $stdContact->name : NULL;
            $email = isset($stdContact->email) ? $stdContact->email : NULL;
            $phone = isset($stdContact->phone) ? $stdContact->phone : NULL;

            $importedEntity = new ImportedEntity;
            $importedEntity->setName($name);
            $importedEntity->setEmailAndPhone($email, $phone);

            $this->addImportedEntity($importedEntity);
        }

        return parent::getImportedEntities();
    }
}
