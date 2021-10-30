<?php

namespace App\Form\Menu;

use App\Entity\Menu;
use App\Entity\Week;
use App\Repository\WeekRepository;
use App\Service\MenuFormHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MenuType extends AbstractType
{
    private Security $security;
    private MenuFormHelper $helper;

    public function __construct(Security $security, MenuFormHelper $helper)
    {
        $this->security = $security;
        $this->helper = $helper;
    }

    /** {@inheritdoc} **/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('week', EntityType::class, [
                'class' => Week::class,
                'choice_label' => 'name',
                'query_builder' => function (WeekRepository $repo) {
                    return $repo->findForUserQuery($this->security->getUser());
                },
            ])
            ->add('date', ChoiceType::class, [
                'choices' => $this->helper->findWeekChoices($this->security->getUser()),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
