<?php

namespace App\Application\Page\Block\Library\Pricing\Main;

use App\Application\Page\Element\Cta\CtaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PricingCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $periods = PricingPeriod::cases();
        $periodChoices = array_combine(
            array_map(
                static fn (PricingPeriod $period): string => $period->label(),
                $periods,
            ),
            $periods,
        );

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', IntegerType::class, [
                'help' => 'Montant en centimes.',
            ])
            ->add('period', ChoiceType::class, [
                'choices' => $periodChoices,
            ])
            ->add('featured', CheckboxType::class, [
                'label' => 'Offre recommandée',
                'required' => false,
            ])
            ->add('showStartingAt', CheckboxType::class, [
                'label' => 'Afficher « À partir de »',
                'required' => false,
            ])
            ->add('features', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__feature__',
            ])
            ->add('cta', CtaType::class, [
                'label' => 'CTA',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PricingCardDTO::class,
        ]);
    }
}
