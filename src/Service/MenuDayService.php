<?php

namespace App\Service;

use App\Entity\Meal;
use App\Entity\MenuDay;
use App\Entity\WeekDay;
use Doctrine\Persistence\ManagerRegistry;

class MenuDayService
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->mealRepo = $this->doctrine->getRepository(Meal::class);
    }

    public function save(MenuDay $menuDay): MenuDay
    {
        $entityMgr = $this->doctrine->getManager();
        $entityMgr->persist($menuDay);
        $entityMgr->flush();

        return $menuDay;
    }

    public function create(WeekDay $weekDay, $date, $excluded): MenuDay
    {
        $meal = $this->findMeal($weekDay, $date, $excluded);

        return (new MenuDay())
            ->setMeal($meal)
            ->setDate($date)
            ->setDay($weekDay->getDay())
            ->setTime($weekDay->getTime())
        ;
    }

    public function changeMeal(MenuDay $menuDay): MenuDay
    {
        $menu = $menuDay->getMenu();
        $forceExcluded = [$menuDay->getMeal()];
        $excluded = [];

        foreach ($menu->getDays() as $day) {
            $excluded[] = $day->getMeal();
        }

        $menuDay->setMeal($this->findMeal(
            $this->findWeekDay($menuDay),
            $menuDay->getDate(),
            $excluded,
            $forceExcluded
        ));

        return $this->save($menuDay);
    }

    public function removeMeal(MenuDay $menuDay): MenuDay
    {
        $menuDay->setMeal(null);

        return $this->save($menuDay);
    }

    public function findWeekDay(MenuDay $menuDay): ?WeekDay
    {
        foreach ($menuDay->getMenu()->getWeek()->getDays() as $wDay) {
            if (
                $wDay->getDay() === $menuDay->getDay() &&
                $wDay->getTime() === $menuDay->getTime()
            ) {
                return $wDay;
            }
        }

        return null;
    }

    public function findMeal(
        WeekDay $weekDay,
        \DateTime $date,
        array $excluded,
        array $forceExcluded = []
    ): ?Meal {
        $meal = $this->mealRepo->findOneForWeekDayAndDate(
            $weekDay,
            $date,
            $excluded
        );

        if (null === $meal) {
            return $this->mealRepo->findOneForWeekDayAndDate(
                $weekDay,
                $date,
                $forceExcluded
            );
        }

        return $meal;
    }
}
