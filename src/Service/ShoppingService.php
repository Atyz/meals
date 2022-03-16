<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\Shopping;
use Doctrine\Persistence\ManagerRegistry;

class ShoppingService
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->shoppingRepo = $this->doctrine->getRepository(Shopping::class);
    }

    public function findForMenu(Menu $menu, int $status = Shopping::STATUS_TO_TAKE)
    {
        $list = [];

        foreach ($this->shoppingRepo->findForMenu($menu, $status) as $shopping) {
            $key = null !== $shopping->getCategory() ? $shopping->getCategory()->getId()->toRfc4122() : -1;

            if (!array_key_exists($key, $list)) {
                $list[$key] = new ShoppingCategory($shopping->getCategory());
            }

            $list[$key]->addShopping($shopping);
        }

        return $list;
    }

    public function findToTakesForMenu(Menu $menu): array
    {
        return $this->findForMenu($menu);
    }

    public function findTakensForMenu(Menu $menu): array
    {
        return $this->findForMenu($menu, Shopping::STATUS_TAKEN);
    }

    public function transferFreeItems(Menu $from, Menu $to): void
    {
        foreach ($from->getShoppings() as $shopping) {
            if (!$shopping->isFree()) {
                continue;
            }

            $free = (new Shopping())
                ->setMenu($to)
                ->setFreename($shopping->getFreename())
                ->setFreecategory($shopping->getFreecategory())
                ->setStatus($shopping->getStatus())
            ;

            $this->doctrine->getManager()->persist($free);
        }

        $this->doctrine->getManager()->flush();
    }
}
