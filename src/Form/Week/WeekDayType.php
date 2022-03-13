<?php

namespace App\Form\Week;

use App\Entity\Meal;
use App\Entity\Theme;
use App\Entity\WeekDay;
use App\Repository\ThemeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class WeekDayType extends AbstractType
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
            ->add('preparations', ChoiceType::class, [
                'choices' => Meal::getPreparations(),
                'multiple' => true,
            ])
            ->add('themes', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'name',
                'multiple' => true,
                'query_builder' => function (ThemeRepository $repo) {
                    return $repo->findForUserQuery($this->security->getUser());
                },
            ])
            ->add('used', CheckboxType::class, [
                'label_attr' => ['class' => 'checkbox-switch'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WeekDay::class,
        ]);
    }
}
