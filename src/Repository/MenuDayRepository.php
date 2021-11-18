<?php

namespace App\Repository;

use App\Entity\MenuDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MenyDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenyDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenyDay[]    findAll()
 * @method MenyDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuDay::class);
    }
}
