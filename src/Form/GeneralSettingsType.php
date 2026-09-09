<?php

namespace App\Form;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeneralSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maintenanceEnabled', CheckboxType::class, ['label' => 'Activer le mode maintenance', 'required' => false])
            ->add('maintenanceMessage', TextareaType::class, ['label' => 'Message aux visiteurs', 'required' => false, 'empty_data' => (new GeneralSettingsDto())->maintenanceMessage, 'attr' => ['rows' => 5]])
            ->add('maintenanceLinks', CollectionType::class, ['entry_type' => MaintenanceLinkType::class, 'allow_add' => true, 'allow_delete' => true, 'required' => false])
            ->add('siteTitle', TextType::class, [
                'label' => 'Titre du site',
                'empty_data' => '',
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'Logo',
                'required' => false,
                'help' => 'JPEG, PNG, WebP ou SVG — 5 Mo maximum.',
                'attr' => ['accept' => 'image/jpeg,image/png,image/webp,image/svg+xml'],
            ])
            ->add('faviconSvgFile', FileType::class, [
                'label' => 'Favicon SVG',
                'required' => false,
                'help' => 'Format SVG — 1 Mo maximum.',
                'attr' => ['accept' => 'image/svg+xml,.svg'],
            ])
            ->add('faviconIcoFile', FileType::class, [
                'label' => 'Favicon ICO',
                'required' => false,
                'help' => 'Format ICO — 1 Mo maximum.',
                'attr' => ['accept' => 'image/x-icon,image/vnd.microsoft.icon,.ico'],
            ])
            ->add('appleTouchIconFile', FileType::class, [
                'label' => 'Icône Apple Touch',
                'required' => false,
                'help' => 'Format PNG — idéalement 180 × 180 px.',
                'attr' => ['accept' => 'image/png'],
            ])
            ->add('timezone', TimezoneType::class, [
                'label' => 'Fuseau horaire',
                'placeholder' => 'Sélectionnez un fuseau horaire',
                'intl' => true,
            ])
            ->add('homepagePageId', ChoiceType::class, [
                'required' => false,
                'label' => 'Page d’accueil',
                'placeholder' => 'Sélectionnez une page',
                'choices' => $options['page_choices'],
                'choice_translation_domain' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneralSettingsDto::class,
            'page_choices' => [],
        ]);
        $resolver->setAllowedTypes('page_choices', 'array');
    }
}
