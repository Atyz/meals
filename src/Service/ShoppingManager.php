<?php

namespace App\Service;

use App\Entity\Shopping;
use Doctrine\Persistence\ManagerRegistry;

class ShoppingManager
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save(Shopping $shopping): Shopping
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($shopping);
        $entityMgr->flush();

        return $shopping;
    }

    public function delete(Shopping $shopping): void
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->remove($shopping);
        $entityMgr->flush();
    }

    public function take(Shopping $shopping): void
    {
        $shopping->setStatus(Shopping::STATUS_TAKEN);
        $this->save($shopping);
    }

    public function untake(Shopping $shopping): void
    {
        $shopping->setStatus(Shopping::STATUS_TO_TAKE);
        $this->save($shopping);
    }
}
