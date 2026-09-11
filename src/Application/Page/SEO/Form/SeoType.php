<?php

namespace App\Application\Page\SEO\Form;

use App\Application\Page\SEO\Dto\SeoDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Titre SEO', 'empty_data' => ''])
            ->add('description', TextareaType::class, ['empty_data' => '', 'attr' => ['rows' => 3]])
            ->add('canonicalUrl', UrlType::class, [
                'label' => 'URL canonique',
                'required' => false,
                'default_protocol' => 'https',
            ])
            ->add('noIndex', CheckboxType::class, ['label' => 'Ne pas indexer cette page', 'required' => false])
            ->add('profilePage', CheckboxType::class, [
                'label' => 'Cette page présente principalement l’identité configurée',
                'help' => 'Active le type Schema.org ProfilePage. À utiliser uniquement pour une page de profil, par exemple « À propos ».',
                'required' => false,
            ])
            ->add('socialTitle', TextType::class, [
                'label' => 'Titre pour les réseaux sociaux',
                'help' => 'Facultatif. Le titre SEO est utilisé par défaut.',
                'required' => false,
            ])
            ->add('socialDescription', TextareaType::class, [
                'label' => 'Description pour les réseaux sociaux',
                'help' => 'Facultative. La description SEO est utilisée par défaut.',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('socialImageFile', FileType::class, [
                'label' => 'Image pour les réseaux sociaux',
                'help' => 'Facultative. Le logo du site est utilisé par défaut lorsqu’il existe.',
                'required' => false,
                'attr' => ['accept' => 'image/jpeg,image/png,image/webp'],
            ])
            ->add('breadcrumbParents', CollectionType::class, [
                'entry_type' => BreadcrumbLinkType::class,
                'label' => 'Pages parentes',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SeoDTO::class]);
    }
}
