<?php

namespace App\Form\Meal;

use App\Entity\Meal;
use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MealCommonType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /** {@inheritdoc} **/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('preparation', ChoiceType::class, [
                'choices' => Meal::getPreparations(),
                'expanded' => true,
            ])
            ->add('recurrence', ChoiceType::class, [
                'choices' => Meal::getRecurrences(),
                'expanded' => true,
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
            'inherit_data' => true,
        ]);
    }
}
