<?php

namespace App\Application\Legal\Form;

use App\Application\Legal\Dto\LegalPageDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LegalPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Titre'])
            ->add('content', TextareaType::class, [
                'label' => 'Texte',
                'sanitize_html' => true,
                'sanitizer' => 'app.legal_sanitizer',
                'attr' => [
                    'rows' => 18,
                    'data-controller' => 'suneditor',
                    'data-suneditor-profile-value' => 'legal',
                    'data-suneditor-max-characters-value' => 50000,
                ],
                'help' => 'Titres H2 à H6, paragraphes et listes uniquement.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => LegalPageDTO::class]);
    }
}
