<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Week;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Week|null find($id, $lockMode = null, $lockVersion = null)
 * @method Week|null findOneBy(array $criteria, array $orderBy = null)
 * @method Week[]    findAll()
 * @method Week[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeekRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Week::class);
    }

    public function findForUserQuery(User $user): QueryBuilder
    {
        return $this
            ->createQueryBuilder('w')
            ->andWhere('w.user = :user')
                ->setParameter(':user', $user->getId(), 'uuid')
        ;
    }

    public function findForUser(User $user): array
    {
        return $this
            ->findForUserQuery($user)
            ->getQuery()
            ->execute()
        ;
    }
}
