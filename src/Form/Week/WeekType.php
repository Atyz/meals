<?php

namespace App\Form\Week;

use App\Entity\Week;
use App\Service\WeekService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeekType extends AbstractType
{
    private WeekService $service;

    public function __construct(WeekService $service)
    {
        $this->service = $service;
    }

    /** {@inheritdoc} **/
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('days', CollectionType::class, [
                'entry_type' => WeekDayType::class,
                'by_reference' => false,
                'data' => $this->service->getAllSortedDays($builder->getData()),
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getForm()->getData();

            foreach ($data->getDays() as $day) {
                if ($day->isEmpty()) {
                    $data->removeDay($day);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Week::class,
        ]);
    }
}
