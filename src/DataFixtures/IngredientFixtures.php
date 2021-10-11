<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    public const INGREDIENT_CHICKEN_UUID = 'de4b5e4f-cd18-4f0f-90ef-f1b27fee1acd';
    public const INGREDIENT_PASTA_UUID = '47532792-f8de-4f82-ba51-01b3a58366fb';
    public const INGREDIENT_RICE_UUID = '1ec2a974-5140-684e-adfd-bbd0b70daf85';

    public function load(ObjectManager $manager): void
    {
        $ingredients = [
            'Escalope de poulet' => self::INGREDIENT_CHICKEN_UUID,
            'Pâtes' => self::INGREDIENT_PASTA_UUID,
            'Riz' => self::INGREDIENT_RICE_UUID,
            'Ebly' => null,
            'Steak haché' => null,
            'Steak de poulet' => null,
        ];

        foreach ($ingredients as $ingredient => $uuid) {
            $manager->persist((new Ingredient($uuid))->setName($ingredient));
        }

        $manager->flush();
    }
}
