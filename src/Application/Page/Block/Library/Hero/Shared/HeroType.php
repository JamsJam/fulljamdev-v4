<?php

namespace App\Application\Page\Block\Library\Hero\Shared;

use App\Application\Page\Element\Badge\BadgeType;
use App\Application\Page\Element\Cta\CtaType;
use App\Application\Page\Element\Heading\HeadingType;
use App\Application\Page\Element\Image\ImageType;
use App\Application\Page\Element\Text\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', HeadingType::class)
            ->add('text', TextType::class)
            ->add('cta1', CtaType::class, ['label' => 'CTA principal', 'required' => false])
            ->add('cta2', CtaType::class, ['label' => 'CTA secondaire', 'required' => false])
            ->add('image', ImageType::class)
            ->add('reverse', CheckboxType::class, [
                'label' => 'Inverser la position de l’image et du contenu',
                'required' => false,
            ])
            ->add('badges', CollectionType::class, [
                'entry_type' => BadgeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => HeroDTO::class]);
    }
}
