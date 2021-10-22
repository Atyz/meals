<?php

namespace App\Service;

use App\Entity\Week;
use Doctrine\Persistence\ManagerRegistry;

class WeekManager
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Week $week): Week
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($week);
        $entityMgr->flush();

        return $week;
    }

    public function del(Week $week): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($week);
        $entityMgr->flush();
    }
}
