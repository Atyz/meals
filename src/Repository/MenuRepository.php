<?php

namespace App\Repository;

use App\Entity\Menu;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findForUserOn(User $user, \DateTime $date): ?Menu
    {
        return $this
            ->createQueryBuilder('m')
            ->andWhere('m.user = :user')
                ->setParameter('user', $user->getId(), 'uuid')
            ->andWhere('m.date = :date')
                ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
