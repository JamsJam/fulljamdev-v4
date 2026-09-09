<?php

namespace App\Application\Page\Block\Library\Faq\Main;

use App\Application\Page\Element\Heading\HeadingType;
use App\Application\Page\Element\Text\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', HeadingType::class)
            ->add('text', TextType::class)
            ->add('items', CollectionType::class, [
                'entry_type' => FaqItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FaqDTO::class]);
    }
}
