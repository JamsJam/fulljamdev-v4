<?php

namespace App\Form;

use App\Application\Reservation\Availability\Dto\AvailabilityDto;
use App\Application\Reservation\Availability\Mapper\DayMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AvailabilityType extends AbstractType
{
    public function __construct(
        private readonly DayMapper $dayMapper,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $days = [];

        for ($number = 1; $number <= 7; ++$number) {
            $days[ucfirst($this->dayMapper->numberToDay($number))] = $number;
        }

        $builder
            ->add('dow', ChoiceType::class, [
                'label' => 'Jour',
                'choices' => $days,
            ])
            ->add('isAvailable', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
            ])
            ->add('startHour', TimeType::class, [
                'label' => 'Heure de début',
                'input' => 'datetime_immutable',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('endHour', TimeType::class, [
                'label' => 'Heure de fin',
                'input' => 'datetime_immutable',
                'required' => false,
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AvailabilityDto::class,
        ]);
    }
}
