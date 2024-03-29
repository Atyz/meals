<?php

namespace App\Repository;

use App\Entity\Menu;
use App\Entity\Shopping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shopping|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shopping|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shopping[]    findAll()
 * @method Shopping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShoppingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shopping::class);
    }

    public function findForMenu(Menu $menu, int $status = Shopping::STATUS_TO_TAKE)
    {
        return $this
            ->createQueryBuilder('s')
            ->leftJoin('s.ingredient', 'i')
            ->leftJoin('i.category', 'c')
            ->andWhere('s.status = :status')
                ->setParameter('status', $status)
            ->andWhere('s.menu = :menu')
                ->setParameter('menu', $menu->getId(), 'uuid')
            ->addOrderBy('c.name', 'asc')
            ->addOrderBy('i.name', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }

    public function cleanForMenuByIds(Menu $menu, array $ids)
    {
        return $this
            ->createQueryBuilder('s')
            ->delete()
            ->andWhere('s.menu = :menu')
                ->setParameter('menu', $menu->getId(), 'uuid')
            ->andWhere('s.ingredient IN (:ids)')
                ->setParameter('ids', $ids)
            ->getQuery()
            ->execute()
        ;
    }
}
