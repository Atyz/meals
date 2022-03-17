<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RefererCodeToUserTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($user): string
    {
        if (null === $user) {
            return '';
        }

        return $user->getRefererCode();
    }

    public function reverseTransform($refererCode): ?User
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['refererCode' => $refererCode])
        ;

        if (null === $user) {
            throw new TransformationFailedException('no user');
        }

        return $user;
    }
}
