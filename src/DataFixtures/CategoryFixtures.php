<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_MEAT_UUID = '1ec5358e-6f0d-655e-9c54-a33ff333662e';
    public const CATEGORY_CARB_UUID = '1ec5358e-4e9a-6a60-a982-3d35e2e8683c';

    public const CATEGORY_MEAT_REF = 'category.meat';
    public const CATEGORY_CARB_REF = 'category.carb';

    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Viandes' => [
                'uuid' => self::CATEGORY_MEAT_UUID,
                'ref' => self::CATEGORY_MEAT_REF,
            ],
            'Féculents' => [
                'uuid' => self::CATEGORY_CARB_UUID,
                'ref' => self::CATEGORY_CARB_REF,
            ],
            'Légumes' => null,
            'Epices' => null,
            'Poissonneries' => null,
            'Charcuteries' => null,
        ];

        foreach ($categories as $name => $data) {
            $uuid = null !== $data ? $data['uuid'] : null;
            $category = (new Category($uuid))->setName($name);
            $manager->persist($category);

            if (null !== $data) {
                $this->addReference($data['ref'], $category);
            }
        }

        $manager->flush();
    }
}
