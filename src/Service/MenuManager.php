<?php

namespace App\Service;

use App\Entity\Menu;
use Doctrine\Persistence\ManagerRegistry;

class MenuManager
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Menu $menu): Menu
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($menu);
        $entityMgr->flush();

        return $menu;
    }

    public function del(Menu $menu): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($menu);
        $entityMgr->flush();
    }
}
