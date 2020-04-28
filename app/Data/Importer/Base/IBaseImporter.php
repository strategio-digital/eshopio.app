<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Data\Importer\Base;

use App\Database\Entity\Project;
use App\Database\Entity\Segment;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Http\FileUpload;

interface IBaseImporter
{
    /**
     * @param FileUpload $fileUpload
     * @return bool
     */
    public function load(FileUpload $fileUpload) : bool;


    /**
     * @return ArrayCollection|ImportedEntity[]
     */
    public function extractAndValidate() : ArrayCollection;

    /**
     * @return int
     */
    public function getExceptionCount() : int;


    /**
     * @param ArrayCollection|ImportedEntity[] $importedEntities
     * @return ArrayCollection|ImportedEntity[]
     */
    public function checkDuplicities(ArrayCollection $importedEntities) : ArrayCollection;

    /**
     * @param Project $project
     * @param ArrayCollection|ImportedEntity[] $importedEntities
     * @return ArrayCollection|ImportedEntity[]
     */
    public function checkExistingData(Project $project, ArrayCollection $importedEntities) : ArrayCollection;


    /**
     * @param Project $project
     * @param Segment $segment
     * @param ArrayCollection|ImportedEntity[] $importedEntities
     */
    public function save(Project $project, Segment $segment, ArrayCollection $importedEntities) : void;
}