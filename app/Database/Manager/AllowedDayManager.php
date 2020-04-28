<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Database\Manager;

use App\Database\Entity\EmailCampaign;
use App\Database\Entity\AllowedDay;
use App\Database\Repository\AllowedDayRepository;
use Doctrine\Common\Collections\ArrayCollection;

class AllowedDayManager extends BaseManager
{
    /**
     * @throws \Exception
     */
    public function createDays() : void
    {
        if (php_sapi_name() !== 'cli') {
           throw new \Exception(AllowedDayManager::class . '::creteDays() method is available only for CLI mode.');
        }

        $this->beginTransaction();

        try {
            foreach (AllowedDayRepository::ALLOWED_DAYS as $name) {
                $allowedDay = new AllowedDay;
                $allowedDay->setName($name);

                $this->em->persist($allowedDay);
                $this->em->flush();
            }
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollback();
            throw $exception;
        }
    }

    /**
     * @param ArrayCollection|AllowedDay[] $allowedDays
     * @param EmailCampaign $emailCampaign
     * @return ArrayCollection|AllowedDay[]
     * @throws \Exception
     */
    public function updateAllowedDays(ArrayCollection $allowedDays, EmailCampaign $emailCampaign) : ArrayCollection
    {
        $dballowedDays = $emailCampaign->getAllowedDays();
        $this->beginTransaction();

        try {
            foreach ($dballowedDays as $dbAllowedDay) {
                if (!$allowedDays->contains($dbAllowedDay)) {
                    $this->removeEmailCampaign($dbAllowedDay, $emailCampaign);
                }
            }

            foreach ($allowedDays as $inputAllowedDay) {
                if (!$dballowedDays->contains($inputAllowedDay)) {
                    $this->appendEmailCampaign($inputAllowedDay, $emailCampaign);
                }
            }
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollback();
            throw $exception;
        }

        return $allowedDays;
    }

    /**
     * @param AllowedDay $allowedDay
     * @param EmailCampaign $emailCampaign
     * @return AllowedDay
     */
    public function appendEmailCampaign(AllowedDay $allowedDay, EmailCampaign $emailCampaign) : AllowedDay
    {
        $allowedDay->getEmailCampaigns()->add($emailCampaign);

        $this->em->persist($allowedDay);
        $this->em->flush();

        return $allowedDay;
    }

    /**
     * @param AllowedDay $allowedDay
     * @param EmailCampaign $emailCampaign
     * @return AllowedDay
     */
    public function removeEmailCampaign(AllowedDay $allowedDay, EmailCampaign $emailCampaign) : AllowedDay
    {
        $allowedDay->getEmailCampaigns()->removeElement($emailCampaign);

        $this->em->persist($allowedDay);
        $this->em->flush();

        return $allowedDay;
    }


}
