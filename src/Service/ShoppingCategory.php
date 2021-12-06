<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Shopping;

class ShoppingCategory
{
    private ?Category $category;
    private array $shoppings = [];

    public function __construct(?Category $category)
    {
        $this->category = $category;
    }

    public function getName()
    {
        if (null === $this->category) {
            return 'Sans rayon';
        }

        return $this->category->getName();
    }

    public function addShopping(Shopping $shopping)
    {
        if (!array_search($shopping, $this->shoppings)) {
            $this->shoppings[] = $shopping;
        }
    }

    public function getShoppings()
    {
        return $this->shoppings;
    }
}
