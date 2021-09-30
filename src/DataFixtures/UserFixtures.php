<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ATYZ_USER_REF = 'atyz.user';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('atyz@meals.fr');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            '123'
        ));

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::ATYZ_USER_REF, $user);
    }
}
