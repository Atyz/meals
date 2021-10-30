<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\User;
use App\Repository\MenuRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuFormHelper
{
    private const MAX_ADVANCE_WEEK = 5;

    private ManagerRegistry $doctrine;
    private MenuRepository $menuRepo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->menuRepo = $this->doctrine->getRepository(Menu::class);
    }

    public function findWeekChoices(User $user): array
    {
        $choices = [];
        $date = new \DateTime('monday this week');

        $choices = array_merge($choices, $this->findWeekChoiceOption($user, $date));

        for ($i = 1; $i < self::MAX_ADVANCE_WEEK; ++$i) {
            $date = clone $date;
            $date->modify('+ 1 week');
            $choices = array_merge($choices, $this->findWeekChoiceOption($user, $date));
        }

        return $choices;
    }

    public function findWeekChoiceOption(User $user, \DateTime $date): array
    {
        $existing = $this->menuRepo->findForUserOn($user, $date);

        $label = sprintf(
            'Semaine %s (du %s au %s)',
            $date->format('W'),
            $date->format('d/m/Y'),
            $date->modify('sunday this week')->format('d/m/Y')
        );

        $date->modify('monday this week');

        $label .= null === $existing ? '' : ' - Déjà généré, sera ecrasé';

        return [$label => $date];
    }
}
