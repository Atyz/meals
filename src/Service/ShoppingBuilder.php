<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\Menu;
use App\Entity\Shopping;
use App\Repository\IngredientRepository;
use App\Repository\ShoppingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class ShoppingBuilder
{
    private ArrayCollection $ingredients;
    private ArrayCollection $ingredientsToDelete;
    private IngredientRepository $ingredientRepo;
    private ShoppingRepository $shoppingRepo;
    private ManagerRegistry $doctrine;
    private Menu $menu;

    public function __construct(
        IngredientRepository $ingredientRepo,
        ShoppingRepository $shoppingRepo,
        ManagerRegistry $doctrine
    ) {
        $this->ingredients = new ArrayCollection();
        $this->ingredientsToDelete = new ArrayCollection();
        $this->ingredientRepo = $ingredientRepo;
        $this->shoppingRepo = $shoppingRepo;
        $this->doctrine = $doctrine;
    }

    public function setMenu(Menu $menu): void
    {
        $this->menu = $menu;
    }

    public function add(Ingredient $ingredient): Shopping
    {
        $shopping = (new Shopping())
            ->setMenu($this->menu)
            ->setIngredient($ingredient)
        ;

        $this->doctrine->getManager()->persist($shopping);

        return $shopping;
    }

    public function handleIngredient(Ingredient $ingredient): void
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $this->add($ingredient);
        }

        if ($this->ingredientsToDelete->contains($ingredient)) {
            $this->ingredientsToDelete->removeElement($ingredient);
        }
    }

    public function cleanIngredients(): void
    {
        $ids = [];

        foreach ($this->ingredientsToDelete as $ingredient) {
            $ids[] = $ingredient->getId()->toBinary();
        }

        $this->shoppingRepo->cleanForMenuByIds($this->menu, $ids);
    }

    public function build(): void
    {
        $baseIngredients = $this->ingredientRepo->findForMenuInShopping($this->menu);

        $this->ingredients = new ArrayCollection($baseIngredients);
        $this->ingredientsToDelete = new ArrayCollection($baseIngredients);

        foreach ($this->menu->getDays() as $day) {
            if (null !== $day->getMeal()) {
                foreach ($day->getMeal()->getIngredients() as $ingredient) {
                    $this->handleIngredient($ingredient);
                }
            }

            foreach ($day->getIngredients() as $ingredient) {
                $this->handleIngredient($ingredient);
            }
        }

        $this->cleanIngredients();
        $this->doctrine->getManager()->flush();
    }
}
