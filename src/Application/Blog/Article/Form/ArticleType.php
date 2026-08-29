<?php

namespace App\Application\Blog\Article\Form;

use App\Application\Blog\Article\Dto\ArticleDto;
use App\Entity\Content\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', null, ['label' => 'Titre'])->add('slug', null, ['label' => 'Slug'])->add('category', EntityType::class, ['class' => Category::class, 'choice_label' => 'name', 'label' => 'Catégorie', 'required' => false, 'placeholder' => 'Sans catégorie'])->add('excerpt', TextareaType::class, ['label' => 'Extrait', 'required' => false, 'attr' => ['rows' => 3]])->add('content', TextareaType::class, ['label' => 'Contenu', 'attr' => ['rows' => 16, 'data-controller' => 'suneditor']])->add('featuredImage', null, ['label' => 'Image principale', 'required' => false])->add('status', ChoiceType::class, ['label' => 'Statut', 'choices' => ['Brouillon' => 'draft', 'Publié' => 'published']])->add('publishedAt', DateTimeType::class, ['label' => 'Date de publication', 'widget' => 'single_text', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ArticleDto::class]);
    }
}
