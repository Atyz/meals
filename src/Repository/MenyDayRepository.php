<?php

namespace App\Repository;

use App\Entity\MenyDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenyDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenyDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenyDay[]    findAll()
 * @method MenyDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenyDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenyDay::class);
    }

    // /**
    //  * @return MenyDay[] Returns an array of MenyDay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MenyDay
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
