<?php

namespace App\Application\Page\Page\Form;

use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\SEO\Form\SeoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['empty_data' => ''])
            ->add('path', null, [
                'empty_data' => '',
                'label' => 'Chemin public',
                'help' => 'Sans slash initial, par exemple : services/developpement.',
            ])
            ->add('seo', SeoType::class)
            ->add('blocks', CollectionType::class, [
                'entry_type' => PageBlockType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PageDTO::class]);
    }
}
