<?php

namespace App\Form\Meal;

use App\Entity\Ingredient;
use App\Entity\Meal;
use App\Entity\Theme;
use App\Repository\ThemeRepository;
use App\Service\MealService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MealType extends AbstractType
{
    private Security $security;
    private MealService $service;

    public function __construct(Security $security, MealService $service)
    {
        $this->security = $security;
        $this->service = $service;
    }

    /** {@inheritdoc} **/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('ingredients', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('preparation', ChoiceType::class, [
                'choices' => Meal::getPreparations(),
            ])
            ->add('recurrence', ChoiceType::class, [
                'choices' => Meal::getRecurrences(),
            ])
            ->add('themes', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function (ThemeRepository $repo) {
                    return $repo->findForUserQuery($this->security->getUser());
                },
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $this->service->setToken($event->getData());
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Meal::class,
        ]);
    }
}
