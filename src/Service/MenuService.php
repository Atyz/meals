<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\User;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuService
{
    private ManagerRegistry $doctrine;
    private MenuManager $manager;
    private MenuDayService $menuDayService;
    private MenuRepository $menuRepo;

    public function __construct(
        ManagerRegistry $doctrine,
        MenuManager $manager,
        MenuDayService $menuDayService
    ) {
        $this->doctrine = $doctrine;
        $this->manager = $manager;
        $this->menuDayService = $menuDayService;
        $this->menuRepo = $this->doctrine->getRepository(Menu::class);
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

            $menuDay = $this->menuDayService->create($weekDay, $date, $excluded);
            $excluded[] = $menuDay->getMeal();

            $menu->addDay($menuDay);
        }

        return $this->manager->save($menu);
    }

    public function findOn(User $user, \DateTime $date): ?Menu
    {
        return $this->menuRepo->findForUserOn($user, $date);
    }
}
