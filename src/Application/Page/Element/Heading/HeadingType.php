<?php

namespace App\Application\Page\Element\Heading;

use App\Application\Page\Element\Attribute\HtmlAttributesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HeadingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', null, ['label' => 'Titre', 'empty_data' => ''])
            ->add('level', EnumType::class, [
                'class' => HeadingLevel::class,
                'label' => 'Niveau',
                'expanded' => true,
                'empty_data' => HeadingLevel::H1->value,
            ])
            ->add('attributes', HtmlAttributesType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => HeadingDTO::class]);
    }
}
