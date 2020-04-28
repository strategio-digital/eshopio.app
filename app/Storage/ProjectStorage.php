<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Storage;

use App\Database\Entity\Project;
use Nette\Http\Session;
use Nette\Http\SessionSection;

class ProjectStorage
{
    /**
     * Storage ID
     */
    const SESSION_SECTION = 'App.Storage.ProjectStorage';

    /**
     * @var SessionSection
     */
    protected SessionSection $section;

    /**
     * ProjectStorage constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->section = $session->getSection(self::SESSION_SECTION);
    }

    /**
     * @param Project $project
     */
    public function setActiveProject(Project $project) : void
    {
        $this->section->project = $project;
    }

    /**
     * @return Project|null
     */
    public function getActiveProject() : ?Project
    {
        return $this->section->project;
    }
}
