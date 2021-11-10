<?php

namespace App\Repository;

use App\Entity\Ingredient;
use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngredientRepository extends ServiceEntityRepository
{
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
}
