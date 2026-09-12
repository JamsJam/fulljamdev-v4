<?php

namespace App\Application\Page\Block\Library\Bento\Main;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BentoCardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('title', TextType::class)->add('text', TextareaType::class)->add('size', ChoiceType::class, ['choices' => ['Standard' => 'standard', 'Large' => 'wide', 'Haute' => 'tall']]);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => BentoCardDTO::class]);
    }
}
