<?php

namespace App\Application\Page\Block\Library\Hero\Positioning;

use App\Application\Page\Element\Cta\CtaType;
use App\Application\Page\Element\Heading\HeadingType;
use App\Application\Page\Element\Image\ImageType;
use App\Application\Page\Element\Text\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PositioningHeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', HeadingType::class)->add('text', TextType::class)->add('image', ImageType::class)
            ->add('cta', CtaType::class, ['required' => false])->add('reverse', CheckboxType::class, ['required' => false])
            ->add('proofs', CollectionType::class, ['entry_type' => SocialProofType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'prototype' => true])
            ->add('cards', CollectionType::class, ['entry_type' => PositioningCardType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'prototype' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PositioningHeroDTO::class]);
    }
}
