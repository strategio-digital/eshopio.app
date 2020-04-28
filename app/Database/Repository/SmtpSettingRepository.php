<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\Project;
use App\Database\Entity\SmtpSetting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;

class SmtpSettingRepository extends EntityRepository
{
    /**
     * @var EntityManagerDecorator
     */
    protected EntityManagerDecorator $em;

    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $repository;

    /**
     * ProjectRepository constructor.
     * @param EntityManagerDecorator $em
     */
    public function __construct(EntityManagerDecorator $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(SmtpSetting::class);
    }

    /**
     * @param Project $project
     * @return ArrayCollection|SmtpSetting[]
     */
    public function findAllByProject(Project $project) : ArrayCollection
    {
        /** @var SmtpSetting[] $smtpSettings */
        $smtpSettings = $this->repository->findBy([
            'project' => $project
        ]);

        return new ArrayCollection($smtpSettings);
    }

    /**
     * @param int $id
     * @return Project|null
     */
    public function findOneById(int $id) : ?SmtpSetting
    {
        /** @var SmtpSetting $smtpSetting */
        $smtpSetting = $this->repository->find($id);

        return $smtpSetting;
    }
}
