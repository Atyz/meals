<?php

namespace App\Service;

use App\Entity\Meal;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MealService
{
    private ValidatorInterface $validator;
    private ArrayHelper $arrayHelper;
    private MealManager $manager;

    public function __construct(
        ValidatorInterface $validator,
        ArrayHelper $arrayHelper,
        MealManager $manager
    ) {
        $this->validator = $validator;
        $this->arrayHelper = $arrayHelper;
        $this->manager = $manager;
    }

    public function generate(User $user, array $datas): array
    {
        $return = ['err' => [], 'meals' => []];

        $preparation = $datas['preparation'];
        $recurrence = $datas['recurrence'];
        $themes = $datas['themes'];

        unset(
            $datas['preparation'],
            $datas['recurrence'],
            $datas['themes'],
        );

        foreach ($this->arrayHelper->combine($datas) as $ingredients) {
            $meal = (new Meal())
                ->setPreparation($preparation)
                ->setRecurrence($recurrence)
                ->setUser($user)
            ;

            foreach ($themes as $theme) {
                $meal->addTheme($theme);
            }

            foreach ($ingredients as $ingredient) {
                $meal->addIngredient($ingredient);
            }

            $this->setNameFromIngredients($meal);
            $this->setToken($meal);

            $errors = $this->validator->validate($meal);

            if (0 < count($errors)) {
                $return['err'][$meal->getName()] = $errors;
                continue;
            }

            $this->manager->save($meal);
            $return['meals'][$meal->getName()] = $meal;
        }

        return $return;
    }

    public function setNameFromIngredients(Meal $meal): Meal
    {
        $name = '';

        foreach ($meal->getIngredients() as $ingredient) {
            $name .= $ingredient->getName().' ';
        }

        $meal->setName(trim($name));

        return $meal;
    }

    public function setToken(Meal $meal): Meal
    {
        $ids = [];

        foreach ($meal->getIngredients() as $ingredient) {
            $ids[] = $ingredient->getId()->jsonSerialize();
        }

        sort($ids);

        $meal->setToken(md5(implode('.', $ids)));

        return $meal;
    }
}
