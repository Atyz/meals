<?php

namespace App\Repository;

use App\Entity\Theme;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[]    findAll()
 * @method Theme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    public function findForUserQuery(User $user): QueryBuilder
    {
        return $this
            ->createQueryBuilder('t')
            ->andWhere('t.user = :user OR t.user IS NULL')
                ->setParameter('user', $user->getId(), 'uuid')
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

    public function findForCloset(): array
    {
        return $this
            ->createQueryBuilder('t')
            ->andWhere('t.user IS NULL')
            ->getQuery()
            ->execute()
        ;
    }
}
