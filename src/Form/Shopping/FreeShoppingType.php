<?php

namespace App\Form\Shopping;

use App\Entity\Category;
use App\Entity\Shopping;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FreeShoppingType extends AbstractType
{
    /** {@inheritdoc} **/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('freename', TextType::class)
            ->add('freecategory', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Autres',
                'query_builder' => function (CategoryRepository $repo) {
                    return $repo->findOrderedQuery();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shopping::class,
            'validation_groups' => ['free'],
        ]);
    }
}
