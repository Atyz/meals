<?php

namespace App\Repository;

use App\Entity\Ingredient;
use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngredientRepository extends ServiceEntityRepository
{
    public const ADMIN_PAGINATOR_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    public function findForMenuInShopping(Menu $menu)
    {
        return $this
            ->createQueryBuilder('i')
            ->innerJoin('i.shoppings', 's')
            ->innerJoin('s.menu', 'm')
            ->andWhere('m.id = :menu')
                ->setParameter('menu', $menu->getId(), 'uuid')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPaginator(int $offset): Paginator
    {
        $query = $this
            ->createQueryBuilder('i')
            ->addOrderBy('i.name', 'asc')
            ->setMaxResults(self::ADMIN_PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
}
