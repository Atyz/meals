<?php

namespace App\DataFixtures;

use App\Entity\Day;
use App\Entity\Menu;
use App\Entity\MenuDay;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MenuFixtures extends Fixture implements DependentFixtureInterface
{
    public const MENU_CLASSIC_UUID = '1ec41ae2-d791-6e9e-8712-57ff8bca2f2a';
    public const MENU_CLASSIC_REF = 'menu.classic';

    public function load(ObjectManager $manager): void
    {
        $week = $this->getReference(WeekFixtures::WEEK_CLASSIC_REF);
        $atyz = $this->getReference(UserFixtures::ATYZ_USER_REF);

        $menu = (new Menu(self::MENU_CLASSIC_UUID))
            ->setWeek($week)
            ->setUser($atyz)
            ->setDate((new \DateTime('monday this week')))
        ;

        foreach ($this->getDayDatas($manager) as $data) {
            $date = (new \DateTime())->setISODate(
                $menu->getDate()->format('Y'),
                $menu->getDate()->format('W'),
                $data['day']
            );

            $day = (new MenuDay())
                ->setDate($date)
                ->setMeal($data['meal'])
                ->setDay($data['day'])
                ->setTime($data['time'])
            ;

            $menu->addDay($day);
        }

        $manager->persist($menu);
        $manager->flush();

        $this->addReference(self::MENU_CLASSIC_REF, $menu);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            MealFixtures::class,
            WeekFixtures::class,
        ];
    }

    private function getDayDatas(ObjectManager $manager): array
    {
        $pastaChicken = $this->getReference(MealFixtures::MEAL_PASTA_CHICKEN_REF);
        $riceChicken = $this->getReference(MealFixtures::MEAL_RICE_CHICKEN_REF);
        $pastaSteak = $this->getReference(MealFixtures::MEAL_PASTA_STEAK_REF);

        return [
            [
                'day' => Day::MONDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $pastaChicken,
            ], [
                'day' => Day::MONDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $riceChicken,
            ], [
                'day' => Day::TUESDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $pastaSteak,
            ], [
                'day' => Day::TUESDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $pastaChicken,
            ], [
                'day' => Day::WEDNESDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $riceChicken,
            ], [
                'day' => Day::WEDNESDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $pastaSteak,
            ], [
                'day' => Day::THURSDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $pastaChicken,
            ], [
                'day' => Day::THURSDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $riceChicken,
            ], [
                'day' => Day::FRIDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $pastaSteak,
            ], [
                'day' => Day::FRIDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $pastaChicken,
            ], [
                'day' => Day::SATURDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $riceChicken,
            ], [
                'day' => Day::SATURDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $pastaSteak,
            ], [
                'day' => Day::SUNDAY,
                'time' => Day::TIME_LUNCH,
                'meal' => $pastaChicken,
            ], [
                'day' => Day::SUNDAY,
                'time' => Day::TIME_DINNER,
                'meal' => $riceChicken,
            ],
        ];
    }
}
