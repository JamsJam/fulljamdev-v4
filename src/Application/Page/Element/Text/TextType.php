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
        $contentOptions = ['label' => 'Texte', 'empty_data' => '', 'attr' => ['rows' => 5]];
        if ($options['rich_text']) {
            $contentOptions += [
                'sanitize_html' => true,
                'sanitizer' => 'app.page_text_sanitizer',
            ];
            $contentOptions['attr'] += [
                'data-controller' => 'suneditor',
                'data-suneditor-profile-value' => 'page-text',
                'data-suneditor-max-characters-value' => 1000,
            ];
        }

        $builder->add('content', TextareaType::class, $contentOptions)
            ->add('attributes', HtmlAttributesType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TextDTO::class, 'rich_text' => false]);
        $resolver->setAllowedTypes('rich_text', 'bool');
    }
}
