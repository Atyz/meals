<?php

namespace App\Service;

class MenuNavigator
{
    public const MAX_WEEK_NEXT = 5;
    public const MAX_WEEK_PREV = 10;
    public const FIRST_DAY_OF_WEEK = 'monday';
    public const LAST_DAY_OF_WEEK = 'sunday';

    private \DateTime $from;
    private \DateTime $to;
    private \DateTime $prev;
    private \DateTime $next;
    private \DateTime $max;
    private \DateTime $min;

    public function setBaseDate(?\DateTime $date = null)
    {
        if (null === $date) {
            $date = new \DateTime();
        }

        $this->from = clone $date;
        $this->from->modify(self::FIRST_DAY_OF_WEEK.' this week');

        $this->to = clone $date;
        $this->to->modify(self::LAST_DAY_OF_WEEK.' this week');

        $this->prev = clone $date;
        $this->prev->modify(self::FIRST_DAY_OF_WEEK.' last week');

        $this->next = clone $date;
        $this->next->modify(self::FIRST_DAY_OF_WEEK.' next week');

        $this->max = (new \DateTime(self::FIRST_DAY_OF_WEEK.' this week'))
            ->modify('+ '.self::MAX_WEEK_NEXT.' weeks');

        $this->min = (new \DateTime(self::FIRST_DAY_OF_WEEK.' this week'))
            ->modify('- '.self::MAX_WEEK_PREV.' weeks');
    }

    public function getFrom(): \DateTime
    {
        return $this->from;
    }

    public function getTo(): \DateTime
    {
        return $this->to;
    }

    public function getNext(): \DateTime
    {
        return $this->next;
    }

    public function getPrev(): \DateTime
    {
        return $this->prev;
    }

    public function canNext(): bool
    {
        return $this->max->format('Y-m-d') > $this->from->format('Y-m-d');
    }

    public function canPrev(): bool
    {
        return $this->min->format('Y-m-d') < $this->from->format('Y-m-d');
    }

    public function isValid(): bool
    {
        return
            $this->from->format('Y-m-d') <= $this->max->format('Y-m-d') &&
            $this->from->format('Y-m-d') >= $this->min->format('Y-m-d')
        ;
    }

    public function isPast(): bool
    {
        $now = (new \DateTime(self::FIRST_DAY_OF_WEEK.' this week'));

        return $this->from->format('Y-m-d') < $now->format('Y-m-d');
    }

    public function isFuture(): bool
    {
        $now = (new \DateTime(self::FIRST_DAY_OF_WEEK.' this week'));
        dump($now, $this->from);

        return $this->from->format('Y-m-d') > $now->format('Y-m-d');
    }
}
