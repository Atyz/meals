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

    public const WEEK_EMPTY_UUID = '1ec48465-d225-6cf2-ba77-a30a0a1c6852';
    public const WEEK_EMPTY_REF = 'week.empty';

    public function load(ObjectManager $manager): void
    {
        $classic = $this->createWeek(
            self::WEEK_CLASSIC_UUID,
            'Semaine normale',
            $this->getDayDatas()
        );

        $empty = $this->createWeek(
            self::WEEK_EMPTY_UUID,
            'Semaine vide',
            $this->getDayDatasForEmpty()
        );

        $manager->persist($classic);
        $manager->persist($empty);
        $manager->flush();

        $this->addReference(self::WEEK_CLASSIC_REF, $classic);
        $this->addReference(self::WEEK_EMPTY_REF, $empty);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ThemeFixtures::class,
        ];
    }

    private function createWeek(string $uuid, string $name, array $datas)
    {
        $atyz = $this->getReference(UserFixtures::ATYZ_USER_REF);

        $week = (new Week($uuid))
            ->setUser($atyz)
            ->setName($name)
        ;

        foreach ($datas as $data) {
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

        return $week;
    }

    private function getDayDatasForEmpty()
    {
        $diet = $this->getReference(ThemeFixtures::THEME_DIET_REF);

        return [
            [
                'day' => Day::MONDAY,
                'time' => Day::TIME_LUNCH,
                'prep' => [Meal::PREP_LONG],
                'themes' => [$diet],
            ],
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
