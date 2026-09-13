<?php

namespace App\Application\Legal\Form;

use App\Application\Legal\Dto\LegalSettingsDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LegalSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('legalNotice', LegalPageType::class, ['label' => false])
            ->add('privacyPolicy', LegalPageType::class, ['label' => false])
            ->add('cookiePolicy', LegalPageType::class, ['label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => LegalSettingsDTO::class]);
    }
}
