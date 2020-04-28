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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImporterXlsx extends BaseImporter
{
    /**
     * @var Worksheet
     */
    protected Worksheet $workSheet;

    /**
     * @param FileUpload $fileUpload
     * @return bool
     * @throws \Exception
     */
    public function load(FileUpload $fileUpload) : bool
    {
        $splFileInfo = $this->saveTempFile($fileUpload);
        $path = $splFileInfo->getRealPath();

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(TRUE);
        $reader->setReadEmptyCells(FALSE);

        try {
            $spreadSheet = $reader->load($path);
            $this->workSheet = $spreadSheet->getActiveSheet();
            $this->removeTempFile($splFileInfo);
        } catch (\Exception $exception) {
            $this->removeTempFile($splFileInfo);
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @return ArrayCollection|ImportedEntity[]
     */
    public function extractAndValidate() : ArrayCollection
    {
        $columnMap = [
            'name' => NULL, // A
            'email' => NULL, // B
            'phone' => NULL // X
        ];

        // Map firs column values as column names into $columnMap
        foreach ($this->workSheet->getRowIterator(1, 1) as $rowKey => $row) {
            foreach ($row->getCellIterator() as $cell) {
                if (key_exists($cell->getValue(), $columnMap)) {
                    $columnMap[$cell->getValue()]= $cell->getColumn();
                }
            }
        }

        // Check if column names are mapped
        if (!$columnMap['name'] || (!$columnMap['email'] && ! $columnMap['phone'])) {
            $importedEntity = new ImportedEntity;

            if (!$columnMap['name']) {
                $importedEntity->addException("Sloupeček 'name' není definován.");
            }

            if (!$columnMap['email'] && ! $columnMap['phone']) {
                $importedEntity->addException("Jeden ze sloupečků 'email / phone' musí být definován.");
            }

            $this->addImportedEntity($importedEntity);
        }

        foreach ($this->workSheet->getRowIterator(2, 1001) as $rowKey => $row) {
            $importedArray = [
                'name' => NULL,
                'email' => NULL,
                'phone' => NULL,
            ];

            foreach ($row->getCellIterator() as $cell) {
                if (in_array($cell->getColumn(), $columnMap)) {
                    $columnType = array_keys($columnMap, $cell->getColumn())[0];
                    $importedArray[$columnType] = $cell->getValue();
                }
            }

            if ($importedArray['name'] && ($importedArray['email'] || $importedArray['phone'])) {
                $importedEntity = new ImportedEntity;
                $importedEntity->setName($importedArray['name']);
                $importedEntity->setEmailAndPhone($importedArray['email'], $importedArray['phone']);

                $this->addImportedEntity($importedEntity);
            }
        }

        return parent::getImportedEntities();
    }
}