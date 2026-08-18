<?php

namespace App\Form;

use App\Entity\Reservation\Summary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppointmentSummaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, ['label' => 'Compte rendu', 'empty_data' => '', 'attr' => ['rows' => 6]])
            ->add('internalNotes', TextareaType::class, ['label' => 'Notes internes', 'required' => false, 'attr' => ['rows' => 3]])
            ->add('recordingLink', UrlType::class, ['label' => 'Lien de l’enregistrement', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Summary::class,
            'csrf_token_id' => 'appointment_summary',
        ]);
    }
}
