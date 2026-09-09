<?php

namespace App\Form;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PublicAppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', AppointmentDateType::class,
                [
                    'slots' => $options['slots'],
                ])
            ->add('time', AppointmentTimeType::class, [
                'slots' => $options['slots'],
                'selected_date' => $options['selected_date'],
                'display_timezone' => $options['display_timezone'],
                'planning_timezone' => $options['planning_timezone'],
            ])
            ->add('contact', AppointmentContactType::class)
            ->add('submit', SubmitType::class, ['label' => 'Confirmer le rendez-vous']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublicAppointmentDto::class,
            'csrf_token_id' => 'public_appointment',
            'selected_date' => null,
            'display_timezone' => 'Europe/Paris',
            'planning_timezone' => 'Europe/Paris',
        ]);
        $resolver->setRequired('slots');
        $resolver->setAllowedTypes('slots', 'array');
        $resolver->setAllowedTypes('selected_date', ['null', 'string']);
        $resolver->setAllowedTypes('display_timezone', 'string');
        $resolver->setAllowedTypes('planning_timezone', 'string');
    }
}
