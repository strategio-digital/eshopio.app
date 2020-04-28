<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\Project;
use App\Database\Entity\User;

class ProjectManager extends BaseManager
{
    /**
     * @param User $user
     * @param string $name
     * @return Project
     * @throws \Exception
     */
    public function create(User $user, string $name) : Project
    {
        $this->em->beginTransaction();

        try {
            $project = new Project;
            $project->setSecretKey();
            $project->setName($name);
            $project->getUsers()->add($user);

            $this->em->persist($project);
            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $exception) {
            $this->em->rollback();
            throw $exception;
        }

        return $project;
    }

    /**
     * @param Project $project
     * @param string $name
     * @return Project
     * @throws \Exception
     */
    public function updateName(Project $project, string $name) : Project
    {
        $project->setName($name);
        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }

    /**
     * @param Project $project
     * @param User $user
     * @return Project
     */
    public function appendUser(Project $project, User $user) : Project
    {
        $project->getUsers()->add($user);
        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }

    /**
     * @param Project $project
     * @param User $user
     * @return Project
     */
    public function removeUser(Project $project, User $user) : Project
    {
        $project->getUsers()->removeElement($user);
        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }
}
