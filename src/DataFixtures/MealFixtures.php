<?php

namespace App\DataFixtures;

use App\Entity\Meal;
use App\Service\MealService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MealFixtures extends Fixture implements DependentFixtureInterface
{
    public const MEAL_PASTA_CHICKEN_REF = 'meal.pasta.chicken';
    public const MEAL_RICE_CHICKEN_REF = 'meal.rice.chicken';
    public const MEAL_PASTA_STEAK_REF = 'meal.pasta.steak';

    private MealService $service;

    public function __construct(MealService $service)
    {
        $this->service = $service;
    }

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
            ->setName('Pâtes Escalope de poulet')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($chicken)
            ->addIngredient($pasta)
            ->addTheme($diet)
        ;
        $this->service->setToken($meal);
        $manager->persist($meal);
        $this->addReference(self::MEAL_PASTA_CHICKEN_REF, $meal);

        $meal = (new Meal())
            ->setUser($atyz)
            ->setName('Riz Escalope de poulet')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($chicken)
            ->addIngredient($rice)
            ->addTheme($diet)
        ;
        $this->service->setToken($meal);
        $manager->persist($meal);
        $this->addReference(self::MEAL_RICE_CHICKEN_REF, $meal);

        $meal = (new Meal())
            ->setUser($atyz)
            ->setName('Pâtes Steak haché')
            ->setPreparation(Meal::PREP_EXPRESS)
            ->addIngredient($steak)
            ->addIngredient($pasta)
            ->addTheme($bdiet)
        ;
        $this->service->setToken($meal);
        $manager->persist($meal);
        $this->addReference(self::MEAL_PASTA_STEAK_REF, $meal);

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
