<?php

namespace App\Service;

use App\Entity\Meal;
use App\Entity\Menu;
use App\Entity\MenuDay;
use App\Entity\User;
use App\Entity\WeekDay;
use App\Repository\MealRepository;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuService
{
    private ManagerRegistry $doctrine;
    private MenuManager $manager;
    private MenuRepository $menuRepo;
    private MealRepository $mealRepo;

    public function __construct(
        ManagerRegistry $doctrine,
        MenuManager $manager
    ) {
        $this->doctrine = $doctrine;
        $this->manager = $manager;
        $this->menuRepo = $this->doctrine->getRepository(Menu::class);
        $this->mealRepo = $this->doctrine->getRepository(Meal::class);
    }

    public function generate(Menu $menu): Menu
    {
        $existing = $this->menuRepo->findForUserOn($menu->getUser(), $menu->getDate());

        if (null !== $existing) {
            $this->manager->del($existing);
        }

        $week = $menu->getWeek();
        $excluded = [];

        foreach ($week->getDays() as $weekDay) {
            $date = (new \DateTime())->setISODate(
                $menu->getDate()->format('Y'),
                $menu->getDate()->format('W'),
                $weekDay->getDay()
            );

            $meal = $this->findMeal($weekDay, $date, $excluded);
            $excluded[] = $meal;
            $excluded = array_filter($excluded);

            $menuDay = (new MenuDay())
                ->setMeal($meal)
                ->setDay($weekDay->getDay())
                ->setTime($weekDay->getTime())
            ;

            $menu->addDay($menuDay);
        }

        return $this->manager->save($menu);
    }

    public function findMeal(WeekDay $weekDay, \DateTime $date, array $excluded): ?Meal
    {
        $meal = $this->mealRepo->findOneForWeekDayAndDate($weekDay, $date, $excluded);

        if (null === $meal) {
            $meal = $this->mealRepo->findOneForWeekDayAndDate($weekDay, $date);
        }

        return $meal;
    }

    public function findCurrent(User $user)
    {
        return $this->menuRepo->findForUserOn(
            $user,
            new \DateTime('monday this week')
        );
    }
}
