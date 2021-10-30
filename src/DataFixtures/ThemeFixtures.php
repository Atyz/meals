<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REQUEST_TOKEN_VALID = 'valid';
    public const REQUEST_TOKEN_EXPIRED = 'expired';

    public const THEME_DIET_UUID = '1e8a5b79-9bdc-4fe4-a1b1-057860f7e664';
    public const THEME_BDIET_UUID = '1ec2a974-5918-64fe-b371-bbd0b70daf85';

    public const THEME_DIET_REF = 'theme.diet';
    public const THEME_BDIET_REF = 'theme.bdiet';

    public function load(ObjectManager $manager): void
    {
        $atyz = $this->getReference(UserFixtures::ATYZ_USER_REF);
        $user = $this->getReference(UserFixtures::USER_USER_REF);

        $themes = [
            'Diète maigre' => [
                'uuid' => self::THEME_DIET_UUID,
                'ref' => self::THEME_DIET_REF,
            ],
            'Diète grasse' => [
                'uuid' => self::THEME_BDIET_UUID,
                'ref' => self::THEME_BDIET_REF,
            ],
            'Vegétarien' => null,
            'Junk' => null,
        ];

        foreach ($themes as $name => $data) {
            $uuid = null !== $data ? $data['uuid'] : null;

            $theme = (new Theme($uuid))
                ->setUser($atyz)
                ->setName($name)
            ;

            $manager->persist($theme);

            if (null !== $data) {
                $this->addReference($data['ref'], $theme);
            }
        }

        $themes = [
            'Classique',
            'Sans gluten',
            'Végétél',
            'Jeûne',
        ];

        foreach ($themes as $name) {
            $theme = (new Theme())
                ->setUser($user)
                ->setName($name)
            ;

            $manager->persist($theme);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
