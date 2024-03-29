<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public const INGREDIENT_CHICKEN_UUID = 'de4b5e4f-cd18-4f0f-90ef-f1b27fee1acd';
    public const INGREDIENT_STEAK_UUID = '1ec2a974-5140-6ede-ba30-bbd0b70daf85';
    public const INGREDIENT_PASTA_UUID = '47532792-f8de-4f82-ba51-01b3a58366fb';
    public const INGREDIENT_RICE_UUID = '1ec2a974-5140-684e-adfd-bbd0b70daf85';

    public const INGREDIENT_CHICKEN_REF = 'ingredient.chicken';
    public const INGREDIENT_STEAK_REF = 'ingredient.steak';
    public const INGREDIENT_PASTA_REF = 'ingredient.pasta';
    public const INGREDIENT_RICE_REF = 'ingredient.rice';

    public function load(ObjectManager $manager): void
    {
        $categoryMeat = $this->getReference(CategoryFixtures::CATEGORY_MEAT_REF);
        $categoryCarb = $this->getReference(CategoryFixtures::CATEGORY_CARB_REF);

        $ingredients = [
            'Escalope de poulet' => [
                'uuid' => self::INGREDIENT_CHICKEN_UUID,
                'ref' => self::INGREDIENT_CHICKEN_REF,
                'category' => $categoryMeat,
            ],
            'Pâtes' => [
                'uuid' => self::INGREDIENT_PASTA_UUID,
                'ref' => self::INGREDIENT_PASTA_REF,
                'category' => $categoryCarb,
            ],
            'Riz' => [
                'uuid' => self::INGREDIENT_RICE_UUID,
                'ref' => self::INGREDIENT_RICE_REF,
            ],
            'Steak haché' => [
                'uuid' => self::INGREDIENT_STEAK_UUID,
                'ref' => self::INGREDIENT_STEAK_REF,
            ],
            'Ebly' => null,
            'Steak de poulet' => null,
        ];

        foreach ($ingredients as $name => $data) {
            $uuid = null !== $data ? $data['uuid'] : null;
            $ingredient = (new Ingredient($uuid))
                ->setName($name)
                ->setSeasonality([])
            ;

            if (null !== $data && array_key_exists('category', $data)) {
                $ingredient->setCategory($data['category']);
            }

            $manager->persist($ingredient);

            if (null !== $data) {
                $this->addReference($data['ref'], $ingredient);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
