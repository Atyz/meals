<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ATYZ_USER_REF = 'atyz.user';
    public const USER_USER_REF = 'user.user';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $atyz = new User();
        $atyz->setEmail('atyz@meals.fr');
        $atyz->setPassword($this->passwordHasher->hashPassword(
            $atyz,
            '123'
        ));

        $user = new User();
        $user->setEmail('user@meals.fr');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            '456'
        ));

        $manager->persist($atyz);
        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::ATYZ_USER_REF, $atyz);
        $this->addReference(self::USER_USER_REF, $user);
    }
}
