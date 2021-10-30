<?php

namespace App\DataFixtures;

use App\Entity\Meal;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MealFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $atyz = $this->getReference(UserFixtures::ATYZ_USER_REF);

        $chicken = $this->getReference(IngredientFixtures::INGREDIENT_CHICKEN_REF);
        $steak = $this->getReference(IngredientFixtures::INGREDIENT_STEAK_REF);
        $pasta = $this->getReference(IngredientFixtures::INGREDIENT_PASTA_REF);
        $rice = $this->getReference(IngredientFixtures::INGREDIENT_RICE_REF);

        $bdiet = $this->getReference(ThemeFixtures::THEME_BDIET_REF);
        $diet = $this->getReference(ThemeFixtures::THEME_DIET_REF);

        $meal = (new Meal())
            ->setUser($atyz)
            ->setName('Poulet Pâtes')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($chicken)
            ->addIngredient($pasta)
            ->addTheme($diet)
        ;
        $manager->persist($meal);

        $meal = (new Meal())
            ->setUser($atyz)
            ->setName('Poulet Riz')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($chicken)
            ->addIngredient($rice)
            ->addTheme($diet)
        ;
        $manager->persist($meal);

        $meal = (new Meal())
            ->setUser($atyz)
            ->setName('Steak Pâtes')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($steak)
            ->addIngredient($pasta)
            ->addTheme($bdiet)
        ;
        $manager->persist($meal);

        $meal = (new Meal())
            ->setUser($atyz)
            ->setName('Steak Riz')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($steak)
            ->addIngredient($rice)
            ->addTheme($bdiet)
        ;
        $manager->persist($meal);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ThemeFixtures::class,
            IngredientFixtures::class,
        ];
    }
}
