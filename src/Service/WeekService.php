<?php

namespace App\Service;

use App\Entity\Week;
use App\Entity\WeekDay;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

class WeekService
{
    public function getAllSortedDays(Week $week): Collection
    {
        $weekDays = WeekDay::getDays();
        $times = WeekDay::getTimes();

        foreach (array_keys($weekDays) as $dayId) {
            foreach (array_keys($times) as $timeId) {
                $weekDay = (new WeekDay())
                    ->setDay($dayId)
                    ->setTime($timeId)
                ;

                $week->addDay($weekDay);
            }
        }

        $orderBy = (Criteria::create())->orderBy([
            'day' => Criteria::ASC,
            'time' => Criteria::ASC,
        ]);

        return $week->getDays()->matching($orderBy);
    }

    public function getOnlyUsedDays(Week $week)
    {
        foreach ($week->getDays() as $day) {
            if (false === $day->isUsed()) {
                $week->removeDay($day);
            }
        }

        return $week->getDays();
    }
}
