<?php

namespace App\Repository;

use App\Entity\Meal;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\WeekDay;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findForUserQuery(User $user): QueryBuilder
    {
        return $this
            ->createQueryBuilder('m')
            ->andWhere('m.user = :user')
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

    public function findOneForWeekDayAndDate(
        WeekDay $weekDay,
        DateTime $date,
        array $excluded = []
    ): ?Meal {
        $builder = $this
            ->findForUserQuery($weekDay->getWeek()->getUser())
            ->addSelect('RAND() as HIDDEN rand')
            ->andWhere("DATE_ADD(m.lastUseAt, m.recurrence, 'week') <= :date OR m.lastUseAt IS NULL")
                ->setParameter(':date', $date)
            ->orderBy('rand')
            ->setMaxResults(1)
        ;

        if (0 < count($excluded)) {
            $ids = array_map(function (Meal $meal) {
                return $meal->getId()->toBinary();
            }, $excluded);

            $builder
                ->andWhere('m.id NOT IN (:excluded)')
                    ->setParameter('excluded', $ids)
            ;
        }

        if (0 < count($weekDay->getThemes())) {
            $ids = $weekDay->getThemes()->map(function (Theme $theme) {
                return $theme->getId()->toBinary();
            });

            $builder
                ->innerJoin('m.themes', 't')
                ->andWhere('t.id IN (:themes)')
                    ->setParameter('themes', $ids)
            ;
        }

        if (0 < count($weekDay->getPreparations())) {
            $builder
                ->andWhere('m.preparation IN (:preparations)')
                ->setParameter('preparations', $weekDay->getPreparations())
            ;
        }

        return $builder->getQuery()->getOneOrNullResult();
    }
}
