<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\Menu;
use App\Entity\Shopping;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class ShoppingService
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->shoppingRepo = $this->doctrine->getRepository(Shopping::class);
        $this->ingredientRepo = $this->doctrine->getRepository(Ingredient::class);
    }

    public function save(Shopping $shopping): Shopping
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($shopping);
        $entityMgr->flush();

        return $shopping;
    }

    public function build(Menu $menu): void
    {
        $ingredients = new ArrayCollection($this->ingredientRepo->findForMenuInShopping($menu));
        $entityMgr = $this->doctrine->getManager();

        foreach ($menu->getDays() as $day) {
            if (null === $day->getMeal()) {
                continue;
            }

            foreach ($day->getMeal()->getIngredients() as $ingredient) {
                if (!$ingredients->contains($ingredient)) {
                    $ingredients->add($ingredient);

                    $shopping = (new Shopping())
                        ->setMenu($menu)
                        ->setIngredient($ingredient)
                    ;

                    $entityMgr->persist($shopping);
                }
            }
        }

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

    public function findForMenu(Menu $menu): array
    {
        return $this->shoppingRepo->findForMenu($menu);
    }
}
