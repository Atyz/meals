<?php

namespace App\DataFixtures;

use App\Entity\Day;
use App\Entity\Meal;
use App\Entity\Week;
use App\Entity\WeekDay;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WeekFixtures extends Fixture implements DependentFixtureInterface
{
    public const WEEK_CLASSIC_UUID = '1ec3801e-4e28-6860-abc7-41eec189e50d';
    public const WEEK_CLASSIC_REF = 'week.classic';

    public function load(ObjectManager $manager): void
    {
        $atyz = $this->getReference(UserFixtures::ATYZ_USER_REF);

        $week = (new Week('1ec3801e-4e28-6860-abc7-41eec189e50d'))
            ->setUser($atyz)
            ->setName('Semaine normale')
        ;

        foreach ($this->getDayDatas() as $data) {
            $day = (new WeekDay())
                ->setUsed(true)
                ->setPreparations($data['prep'])
                ->setDay($data['day'])
                ->setTime($data['time'])
            ;

            foreach ($data['themes'] as $theme) {
                $day->addTheme($theme);
            }

            $week->addDay($day);
        }

        $manager->persist($week);
        $manager->flush();

        $this->addReference(self::WEEK_CLASSIC_REF, $week);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ThemeFixtures::class,
        ];
    }

    private function getDayDatas(): array
    {
        $diet = $this->getReference(ThemeFixtures::THEME_DIET_REF);
        $bdiet = $this->getReference(ThemeFixtures::THEME_BDIET_REF);

        return [
            [
                'day' => Day::MONDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::MONDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ], [
                'day' => Day::TUESDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::TUESDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ], [
                'day' => Day::WEDNESDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::WEDNESDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ], [
                'day' => Day::THURSDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::THURSDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ], [
                'day' => Day::FRIDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::FRIDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ], [
                'day' => Day::SATURDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::SATURDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ], [
                'day' => Day::SUNDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$diet],
            ], [
                'day' => Day::SUNDAY,
                'time' => Day::TIME_DINNER,
                'prep' => [Meal::PREP_EXPRESS],
                'themes' => [$bdiet],
            ],
        ];
    }
}
