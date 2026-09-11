<?php

namespace App\Application\Page\Block\Library\CardDisplay\DisplayCardWithLogo;

use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardWithLogoType;
use App\Application\Page\Element\Cta\CtaType;
use App\Application\Page\Element\Heading\HeadingType;
use App\Application\Page\Element\Text\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DisplayCardWithLogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', HeadingType::class)->add('text', TextType::class)
            ->add('cards', CollectionType::class, ['entry_type' => CardWithLogoType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'prototype' => true])
            ->add('cta', CtaType::class, ['label' => 'CTA sous les cartes', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CardDisplayDTO::class]);
    }
}
