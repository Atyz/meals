<?php

namespace App\Repository;

use App\Entity\Meal;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Meal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meal[]    findAll()
 * @method Meal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meal::class);
    }

    public function findForUser(User $user): array
    {
        return $this
            ->createQueryBuilder('m')
            ->andWhere('m.user = :user')
                ->setParameter(':user', $user->getId(), 'uuid')
            ->getQuery()
            ->execute()
        ;
    }
}
