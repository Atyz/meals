<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryRepository[]    findAll()
 * @method CategoryRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public const ADMIN_PAGINATOR_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findOrderedQuery()
    {
        return $this
            ->createQueryBuilder('c')
            ->addOrderBy('c.name', 'asc')
        ;
    }

    public function getPaginator(int $offset): Paginator
    {
        $query = $this
            ->findOrderedQuery()
            ->setMaxResults(self::ADMIN_PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
