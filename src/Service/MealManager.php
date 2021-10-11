<?php

namespace App\Service;

use App\Entity\Meal;
use Doctrine\Persistence\ManagerRegistry;

class MealManager
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Meal $meal): Meal
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($meal);
        $entityMgr->flush();

        return $meal;
    }

    public function del(Meal $meal): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($meal);
        $entityMgr->flush();
    }
}
