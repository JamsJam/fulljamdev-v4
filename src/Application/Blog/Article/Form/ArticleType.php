<?php

namespace App\Application\Blog\Article\Form;

use App\Application\Blog\Article\Dto\ArticleDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ArticleType extends AbstractType
{
    public function __construct(private readonly UrlGeneratorInterface $urls)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Titre', 'required' => false])
            ->add('categoryName', TextType::class, [
                'label' => 'Catégorie',
                'required' => false,
                'help' => 'Sélectionnez une catégorie ou saisissez-en une nouvelle.',
                'attr' => [
                    'autocomplete' => 'off',
                    'data-controller' => 'category-autocomplete',
                    'data-category-autocomplete-url-value' => $this->urls->generate('app_dashboard_blog_category_autocomplete'),
                ],
            ])
            ->add('summary', TextareaType::class, ['label' => 'Résumé', 'required' => false, 'attr' => ['rows' => 3, 'maxlength' => 160]])
            ->add('content', TextareaType::class, ['label' => 'Contenu', 'required' => false, 'attr' => ['rows' => 16, 'data-controller' => 'suneditor', 'data-suneditor-max-characters-value' => 50000]])
            ->add('coverImageFile', FileType::class, [
                'label' => 'Image de couverture',
                'required' => false,
                'help' => 'JPEG, PNG ou WebP — 5 Mo maximum.',
                'attr' => ['accept' => 'image/jpeg,image/png,image/webp'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ArticleDto::class]);
    }
}
