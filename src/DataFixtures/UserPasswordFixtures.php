<?php

namespace App\DataFixtures;

use App\Entity\UserPassword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserPasswordFixtures extends Fixture
{
    public const REQUEST_TOKEN_VALID = 'valid';
    public const REQUEST_TOKEN_EXPIRED = 'expired';

    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference(UserFixtures::ATYZ_USER_REF);

        $validRequest = (new UserPassword())
            ->setUser($user)
            ->setExpireAt(new \DateTime('+ 1 hour'))
            ->setToken(self::REQUEST_TOKEN_VALID)
        ;

        $expiredRequest = (new UserPassword())
            ->setUser($user)
            ->setExpireAt(new \DateTime('- 1 hour'))
            ->setToken(self::REQUEST_TOKEN_EXPIRED)
        ;

        $manager->persist($validRequest);
        $manager->persist($expiredRequest);
        $manager->flush();
    }
}
