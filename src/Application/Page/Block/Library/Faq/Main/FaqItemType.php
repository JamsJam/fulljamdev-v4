<?php

namespace App\Application\Page\Block\Library\Faq\Main;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FaqItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', null, ['label' => 'Question', 'empty_data' => ''])
            ->add('answer', TextareaType::class, [
                'label' => 'Réponse',
                'empty_data' => '',
                'sanitize_html' => true,
                'sanitizer' => 'app.page_text_sanitizer',
                'attr' => [
                    'data-controller' => 'suneditor',
                    'data-suneditor-profile-value' => 'page-text',
                    'data-suneditor-max-characters-value' => 2000,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => FaqItemDTO::class]);
    }
}
