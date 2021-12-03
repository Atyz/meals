<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;

class CategoryManager
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Category $category): Category
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($category);
        $entityMgr->flush();

        return $category;
    }

    public function del(Category $category): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($category);
        $entityMgr->flush();
    }
}
