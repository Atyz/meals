<?php

namespace App\Form\Meal;

use App\Entity\Ingredient;
use App\Entity\Meal;
use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MealGenerateType extends AbstractType
{
    public const TOTAL_FIELDS = 3;
    public const MAX_INGREDIENTS = 5;

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** {@inheritdoc} **/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        for ($i = 1; $i <= self::TOTAL_FIELDS; ++$i) {
            $builder
                ->add('ingredient'.$i, EntityType::class, [
                    'class' => Ingredient::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'constraints' => [new Count(
                        null,
                        null,
                        self::MAX_INGREDIENTS,
                        null,
                        null,
                        null,
                        'Merci de ne sélectionner qu\'au maximum {{ limit }} ingrédients par ensemble.'
                    )],
                ])
            ;
        }

        $builder
            ->add('preparation', ChoiceType::class, [
                'choices' => Meal::getPreparations(),
                'expanded' => true,
                'constraints' => [new NotBlank(
                    null,
                    'Merci d\'indiquer un temps de préparation.'
                )],
            ])
            ->add('recurrence', ChoiceType::class, [
                'choices' => Meal::getRecurrences(),
                'constraints' => [new NotBlank(
                    null,
                    'Merci d\'indiquer une récurrence.'
                )],
            ])
            ->add('themes', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function (ThemeRepository $repo) {
                    return $repo->findForUserQuery($this->security->getUser());
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [new Callback([$this, 'validate'])],
        ]);
    }

    public function validate(array $data, ExecutionContextInterface $context): void
    {
        $notEmpties = 0;

        for ($i = 1; $i <= self::TOTAL_FIELDS; ++$i) {
            $key = 'ingredient'.$i;

            if (array_key_exists($key, $data) && 0 < count($data[$key])) {
                ++$notEmpties;
            }
        }

        if (2 > $notEmpties) {
            $context
                ->buildViolation('Merci de sélectionner un ou plusieurs ingrédients dans au moins 2 ensembles.')
                ->addViolation()
            ;
        }
    }
}
