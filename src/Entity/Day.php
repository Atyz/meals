<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class Day
{
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;
    public const SUNDAY = 7;

    public const TIME_LUNCH = 1;
    public const TIME_DINNER = 2;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $day;

    /**
     * @ORM\Column(type="integer")
     */
    protected int $time;

    public static function getDays(): array
    {
        return [
            self::MONDAY => 'lundi',
            self::TUESDAY => 'mardi',
            self::WEDNESDAY => 'mercredi',
            self::THURSDAY => 'jeudi',
            self::FRIDAY => 'vendredi',
            self::SATURDAY => 'samedi',
            self::SUNDAY => 'dimanche',
        ];
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getDayLabel()
    {
        $labels = self::getDays();

        return $labels[$this->getDay()] ?? '-';
    }

    public static function getTimes(): array
    {
        return [
            self::TIME_LUNCH => 'déjeuner',
            self::TIME_DINNER => 'dîner',
        ];
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTimeLabel()
    {
        $labels = self::getTimes();

        return $labels[$this->getTime()] ?? '-';
    }
}
