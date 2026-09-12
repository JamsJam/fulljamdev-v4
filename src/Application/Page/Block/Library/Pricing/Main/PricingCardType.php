<?php

namespace App\Application\Page\Block\Library\Pricing\Main;

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
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('title', TextType::class)->add('description', TextareaType::class)->add('price', IntegerType::class, ['help' => 'Montant en centimes.'])->add('period', ChoiceType::class, ['choices' => array_combine(array_map(fn (PricingPeriod $p) => $p->label(), PricingPeriod::cases()), PricingPeriod::cases())])->add('featured', CheckboxType::class, ['label' => 'Offre recommandée', 'required' => false])->add('features', CollectionType::class, ['entry_type' => TextType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'prototype' => true, 'prototype_name' => '__feature__']);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => PricingCardDTO::class]);
    }
}
