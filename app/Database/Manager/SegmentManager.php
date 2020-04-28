<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\Project;
use App\Database\Entity\Segment;
use Doctrine\Common\Collections\ArrayCollection;

class SegmentManager extends BaseManager
{
    /**
     * @param Project $project
     * @param string $name
     * @return Segment
     */
    public function create(Project $project, string $name) : Segment
    {
        $segment = new Segment;
        $segment->setProject($project);
        $segment->setName($name);

        $this->em->persist($segment);
        $this->em->flush();

        return $segment;
    }

    /**
     * @param Segment $segment
     * @param string $name
     * @return Segment
     */
    public function updateName(Segment $segment, string $name) : Segment
    {
        $segment->setName($name);

        $this->em->persist($segment);
        $this->em->flush();

        return $segment;
    }

    /**
     * @param ArrayCollection|Segment[] $segments
     * @param EmailCampaign $emailCampaign
     * @return ArrayCollection|Segment[]
     * @throws \Exception
     */
    public function updateSegments(ArrayCollection $segments, EmailCampaign $emailCampaign) : ArrayCollection
    {
        $dbSegments = $emailCampaign->getSegments();
        $this->beginTransaction();

        try {
            foreach ($dbSegments as $dbSegment) {
                if (!$segments->contains($dbSegment)) {
                    $this->removeEmailCampaign($dbSegment, $emailCampaign);
                }
            }

            foreach ($segments as $inputSegment) {
                if (!$dbSegments->contains($inputSegment)) {
                    $this->appendEmailCampaign($inputSegment, $emailCampaign);
                }
            }
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollback();
            throw $exception;
        }

        return $segments;
    }

    /**
     * @param Segment $segment
     * @param EmailCampaign $emailCampaign
     * @return Segment
     */
    public function appendEmailCampaign(Segment $segment, EmailCampaign $emailCampaign) : Segment
    {
        $segment->getEmailCampaigns()->add($emailCampaign);

        $this->em->persist($segment);
        $this->em->flush();

        return $segment;
    }

    /**
     * @param Segment $segment
     * @param EmailCampaign $emailCampaign
     * @return Segment
     */
    public function removeEmailCampaign(Segment $segment, EmailCampaign $emailCampaign) : Segment
    {
        $segment->getEmailCampaigns()->removeElement($emailCampaign);

        $this->em->persist($segment);
        $this->em->flush();

        return $segment;
    }

    /**
     * @param Segment $segment
     */
    public function remove(Segment $segment) : void
    {
        $this->em->remove($segment);
        $this->em->flush();
    }
}
