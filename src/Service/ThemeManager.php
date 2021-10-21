<?php

namespace App\Service;

use App\Entity\Theme;
use Doctrine\Persistence\ManagerRegistry;

class ThemeManager
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Theme $theme): Theme
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($theme);
        $entityMgr->flush();

        return $theme;
    }

    public function del(Theme $theme): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($theme);
        $entityMgr->flush();
    }
}
