<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Component\Contact\Summary;

use App\Component\BaseComponent;
use App\Database\Entity\Project;
use App\Database\Repository\ContactRepository;
use Nette\Utils\Paginator;

class Summary extends BaseComponent
{
    const ITEMS_PER_PAGE = 50;

    /**
     * @var ContactRepository
     */
    protected ContactRepository $contactRepository;

    /**
     * @var Project
     */
    protected Project $activeProject;

    /**
     * @var int
     * @persistent
     */
    public int $page = 1;

    /**
     * Summary constructor.
     * @param ContactRepository $contactRepository
     * @param Project $activeProject
     */
    public function __construct(ContactRepository $contactRepository, Project $activeProject)
    {
        $this->contactRepository = $contactRepository;
        $this->activeProject = $activeProject;
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function render() : void
    {
        $count = $this->contactRepository->countByProject($this->activeProject);

        $paginator = new Paginator;
        $paginator->setItemCount($count);
        $paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
        $paginator->setPage($this->page);

        $contacts = $this->contactRepository->findPaginatedByProject($this->activeProject, $paginator->getLength(), $paginator->getOffset());

        $this->template->render(__DIR__ . '/templates/summary.latte', [
            'contacts' => $contacts,
            'paginator' => $paginator,
            'count' => $count
        ]);
    }

    public function handlePaginate(int $page) : void
    {
        $this->page = $page;
    }
}
