<?php

namespace App\Application\Page\Element\Text;

use App\Application\Page\Element\Attribute\HtmlAttributesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', TextareaType::class, ['label' => 'Texte', 'empty_data' => '', 'attr' => ['rows' => 5]])
            ->add('attributes', HtmlAttributesType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TextDTO::class]);
    }
}
