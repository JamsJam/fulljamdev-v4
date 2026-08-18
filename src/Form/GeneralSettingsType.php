<?php

namespace App\Form;

use App\Application\Settings\Dto\GeneralSettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeneralSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timezone', TimezoneType::class, [
                'label' => 'Fuseau horaire',
                'placeholder' => 'Sélectionnez un fuseau horaire',
                'intl' => true,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => GeneralSettingsDto::class]);
    }
}
