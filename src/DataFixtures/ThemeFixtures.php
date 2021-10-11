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

    public function load(ObjectManager $manager): void
    {
        $atyz = $this->getReference(UserFixtures::ATYZ_USER_REF);
        $user = $this->getReference(UserFixtures::USER_USER_REF);

        $themes = [
            'Diète maigre' => self::THEME_DIET_UUID,
            'Diète grasse' => self::THEME_BDIET_UUID,
            'Vegétarien' => null,
            'Junk' => null,
        ];

        foreach ($themes as $themeName => $uuid) {
            $theme = (new Theme($uuid))
                ->setUser($atyz)
                ->setName($themeName)
            ;

            $manager->persist($theme);
        }

        $themes = [
            'Classique',
            'Sans gluten',
            'Végétél',
            'Jeûne',
        ];

        foreach ($themes as $themeName) {
            $theme = (new Theme())
                ->setUser($user)
                ->setName($themeName)
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
