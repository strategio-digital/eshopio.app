<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Segment\Summary;

use App\Component\BaseComponent;
use App\Database\Entity\Project;
use App\Database\Repository\SegmentRepository;

class Summary extends BaseComponent
{
    /**
     * @var SegmentRepository
     */
    protected SegmentRepository $segmentRepository;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * Summary constructor.
     * @param SegmentRepository $segmentRepository
     * @param Project $activeProject
     */
    public function __construct(SegmentRepository $segmentRepository, Project $activeProject)
    {
        $this->segmentRepository = $segmentRepository;
        $this->activeProject = $activeProject;
    }

    /**
     * Render
     */
    public function render() : void
    {
        $segments = $this->segmentRepository->findAllByProject($this->activeProject);

        $this->template->render(__DIR__ . '/templates/summary.latte', [
            'segments' => $segments
        ]);
    }
}
