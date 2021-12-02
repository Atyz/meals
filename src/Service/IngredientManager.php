<?php

namespace App\Service;

use App\Entity\Ingredient;
use Doctrine\Persistence\ManagerRegistry;

class IngredientManager
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Ingredient $ingredient): Ingredient
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($ingredient);
        $entityMgr->flush();

        return $ingredient;
    }

    public function del(Ingredient $ingredient): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($ingredient);
        $entityMgr->flush();
    }
}
