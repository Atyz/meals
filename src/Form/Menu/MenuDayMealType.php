<?php

namespace App\Form\Menu;

use App\Entity\Meal;
use App\Entity\MenuDay;
use App\Repository\MealRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MenuDayMealType extends AbstractType
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
            ->add('meal', EntityType::class, [
                'class' => Meal::class,
                'choice_label' => 'name',
                'query_builder' => function (MealRepository $repo) {
                    return $repo->findForUserQuery($this->security->getUser());
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuDay::class,
        ]);
    }
}
