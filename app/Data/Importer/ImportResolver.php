<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Data\Importer;

use App\Data\Importer\Base\IBaseImporter;
use Nette\Http\FileUpload;

class ImportResolver
{
    /**
     * Max file size for JSON or XLSX file
     */
    const MAX_FILE_SIZE = 1024 * 1024 * 5; // 5 MB

    /**
     * Allowed mime types for imported file
     */
    const ALLOWED_MIME_TYPES = [
        'application/json' => ImporterJson::class,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ImporterXlsx::class
    ];

    /**
     * @var IBaseImporter[]
     */
    protected array $importers;

    /**
     * ImportResolver constructor.
     * @param ImporterJson $jsonImporter
     * @param ImporterXlsx $xlsxImporter
     */
    public function __construct(ImporterJson $jsonImporter, ImporterXlsx $xlsxImporter)
    {
        $this->importers = [
            ImporterJson::class => $jsonImporter,
            ImporterXlsx::class => $xlsxImporter
        ];
    }

    /**
     * @return int
     */
    public function getMaxFileSize() : int
    {
        return self::MAX_FILE_SIZE;
    }

    /**
     * @return string[]
     */
    public function getAllowedMimeTypes() : array
    {
        $keys = array_keys(self::ALLOWED_MIME_TYPES);
        return $keys;
    }

    /**
     * @param FileUpload $fileUpload
     * @return IBaseImporter
     */
    public function getImporter(FileUpload $fileUpload) : IBaseImporter
    {
        $mimeType = $fileUpload->getContentType();
        $className = self::ALLOWED_MIME_TYPES[$mimeType];

        /** @var IBaseImporter $importer */
        $importer = $this->importers[$className];

        return $importer;
    }
}