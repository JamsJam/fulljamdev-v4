<?php

namespace App\Form;

use App\Entity\Reservation\Appointment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppointmentRescheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, ['label' => 'Nouveau début', 'widget' => 'single_text'])
            ->add('endAt', DateTimeType::class, ['label' => 'Nouvelle fin', 'widget' => 'single_text']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
            'csrf_token_id' => 'appointment_reschedule',
        ]);
    }
}
