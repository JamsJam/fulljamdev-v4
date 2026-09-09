<?php

namespace App\Form;

use App\Application\Reservation\Appointment\Dto\AppointmentContactDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppointmentContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom', 'required' => false, 'attr' => ['placeholder' => 'Votre prénom']])
            ->add('lastName', TextType::class, ['label' => 'Nom', 'required' => false, 'attr' => ['placeholder' => 'Votre nom']])
            ->add('email', EmailType::class, ['label' => 'Email', 'required' => false, 'attr' => ['placeholder' => 'nom@exemple.com']])
            ->add('phoneNumber', TelType::class, ['label' => 'Téléphone', 'required' => false, 'attr' => ['placeholder' => 'Votre numéro de téléphone']])
            ->add('reason', TextareaType::class, [
                'label' => 'Raison du rendez-vous',
                'required' => false,
                'attr' => ['placeholder' => 'Décrivez brièvement la raison du rendez-vous...', 'rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => AppointmentContactDto::class]);
    }
}
