<?php

namespace App\Application\Page\Block\Library\Testimonials\Main;

use App\Application\Page\Element\Heading\HeadingType;
use App\Application\Page\Element\Text\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TestimonialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $b->add('title', HeadingType::class)->add('text', TextType::class)->add('testimonials', CollectionType::class, ['entry_type' => TestimonialType::class, 'allow_add' => true, 'allow_delete' => true, 'by_reference' => false, 'prototype' => true]);
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults(['data_class' => TestimonialsDTO::class]);
    }
}
