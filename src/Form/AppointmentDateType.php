<?php

namespace App\Form;

use App\Application\Reservation\Appointment\Dto\AppointmentDateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppointmentDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        foreach (array_keys($options['slots']) as $date) {
            $choices[$date] = $date;
        }

        $builder->add('value', ChoiceType::class, [
            'label' => '',
            'required' => false,
            'choices' => $choices,
            'expanded' => true,
            'choice_name' => static fn (mixed $choice, mixed $key, string $value): string => str_replace('-', '_', $value),
            'choice_attr' => static fn (): array => [
                'data-action' => 'change->public-booking#selectDate',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => AppointmentDateDto::class]);
        $resolver->setRequired('slots');
        $resolver->setAllowedTypes('slots', 'array');
    }
}
